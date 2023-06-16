<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Console\Cron\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Comm\Amazon\ApplyLogs;
use Magento\Amazon\Comm\Amazon\PullLogs;
use Magento\Amazon\Comm\Amazon\PushUpdates;
use Magento\Amazon\Comm\Amazon\UpdateAccount;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\AccountRepository;
use Magento\Framework\App\State;
use Magento\Framework\Lock\LockManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Run
 *
 * Adding in a console command to isolate ASC listing state machine
 * logic that is normally run via ASC cron task.
 */
class Run extends Command
{
    /**
     * Lock name used to ensure that there's only one instance of synchronization running
     */
    public const LOCK_NAME = 'cli_channels_amazon';

    /** @var State */
    private $appState;

    /** @var AscClientLogger $logger */
    private $logger;
    /**
     * @var LockManagerInterface
     */
    private $lockManager;
    /**
     * @var AccountRepository
     */
    private $accountRepository;
    /**
     * @var PullLogs
     */
    private $pullLogs;
    /**
     * @var ApplyLogs
     */
    private $applyLogs;
    /**
     * @var PushUpdates
     */
    private $pushUpdates;
    /**
     * @var UpdateAccount
     */
    private $updateAccount;

    /**
     * Constructor
     *
     * @param State $appState
     * @param AscClientLogger $logger
     * @param LockManagerInterface $lockManager
     * @param AccountRepository $accountRepository
     * @param PullLogs $pullLogs
     * @param ApplyLogs $applyLogs
     * @param PushUpdates $pushUpdates
     * @param UpdateAccount $updateAccount
     */
    public function __construct(
        State $appState,
        AscClientLogger $logger,
        LockManagerInterface $lockManager,
        AccountRepository $accountRepository,
        PullLogs $pullLogs,
        ApplyLogs $applyLogs,
        PushUpdates $pushUpdates,
        UpdateAccount $updateAccount
    ) {
        $this->appState = $appState;
        $this->logger = $logger;
        $this->lockManager = $lockManager;
        $this->accountRepository = $accountRepository;
        $this->pullLogs = $pullLogs;
        $this->applyLogs = $applyLogs;
        $this->pushUpdates = $pushUpdates;
        $this->updateAccount = $updateAccount;
        parent::__construct(null);
    }

    /**
     * Renders the CLI command name and description to the user.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('channel:amazon:run');
        $this->setDescription('Run the Amazon Sales Channel sync.');
        $this->setHelp(
            <<<HELP
By default, runs on all merchants if no Merchant UUIDs specified
HELP
        );
        $this->addOption(
            'no-pull',
            '',
            InputOption::VALUE_NONE,
            'Do not pull updates from the Amazon'
        );
        $this->addOption(
            'no-apply',
            '',
            InputOption::VALUE_NONE,
            'Do not apply updates previously pulled from the Amazon'
        );
        $this->addOption(
            'no-push',
            '',
            InputOption::VALUE_NONE,
            'Do not push collected updates to the Amazon'
        );
        $this->addOption(
            'no-merchant-update',
            '',
            InputOption::VALUE_NONE,
            'Do not fetch account status from SaaS'
        );
        $this->addArgument(
            'merchants',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Merchant UUIDs to sync (all if not specified)'
        );
        $this->addOption(
            'fail-fast',
            'F',
            InputOption::VALUE_NONE,
            'Exit earlier in case of errors'
        );
    }

    /**
     * Execute the ASC listing state machine logic if the command from the user is invoked.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('CLI used to run ASC cron jobs.');

        if ($this->lockManager->isLocked('CRON_GROUP_channel_amazon')) {
            $this->logger->info('Stopping CLI execution in parallel to cron job.');
            $output->writeln('Stopping command execution because it runs in parallel to cron job');
            return 1;
        }
        if ($this->lockManager->isLocked(self::LOCK_NAME)) {
            $this->logger->info('Stopping CLI execution in parallel to another command.');
            $output->writeln(
                'Stopping command execution because it runs in parallel to another instance of the command'
            );
            return 1;
        }

        if (!$this->lockManager->lock(self::LOCK_NAME, 5)) {
            $this->logger->info('Cannot acquire lock for the command.');
            $output->writeln('Cannot acquire lock for the command.');
            return 1;
        }

        $runAccountUpdate = !$input->getOption('no-merchant-update');
        $runPull = !$input->getOption('no-pull');
        $runApply = !$input->getOption('no-apply');
        $runPush = !$input->getOption('no-push');
        /** @var boolean $failFast */
        $failFast = $input->getOption('fail-fast');

        $merchantUUIDs = $input->getArgument('merchants')
            ? array_flip($input->getArgument('merchants'))
            : null;

        $accountsToProcess = $this->getMatchingMerchantAccounts($merchantUUIDs);
        if (!$accountsToProcess) {
            if ($merchantUUIDs === null) {
                $output->writeln("<info>'No active accounts found'</info>");
            } else {
                $output->writeln("<error>No active accounts found matching specified UUIDs</error>");
            }
            return 0;
        }

        try {
            $this->appState->setAreaCode('adminhtml');
            foreach ($accountsToProcess as $account) {
                $output->writeln(
                    sprintf(
                        "<info>Syncing account %s (UUID %s)</info>",
                        $account->getName(),
                        $account->getUuid()
                    ),
                    OutputInterface::VERBOSITY_VERBOSE
                );
                $output->writeln('', OutputInterface::VERBOSITY_VERY_VERBOSE);
                if ($runAccountUpdate) {
                    $this->updateAccount($account, $failFast, $output);
                }
                if (!$account->getIsActive()) {
                    $output->writeln(
                        sprintf(
                            "<info>Account %s (UUID %s) is no longer active</info>",
                            $account->getName(),
                            $account->getUuid()
                        ),
                        OutputInterface::VERBOSITY_NORMAL
                    );
                    continue;
                }
                if ($runPull) {
                    $this->pull($account, $failFast, $output);
                }
                if ($runApply) {
                    $this->apply($account, $failFast, $output);
                }
                if ($runPush) {
                    $this->push($account, $failFast, $output);
                }
            }
        } catch (\Throwable $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            $this->logger->critical(
                'Exception occurred during Amazon Cron run using CLI',
                [
                    'exception' => $exception
                ]
            );
        } finally {
            $this->lockManager->unlock(self::LOCK_NAME);
        }
        return 0;
    }

    private function catchException(
        \Throwable $exception,
        AccountInterface $account,
        OutputInterface $output,
        string $message,
        bool $isFailFast
    ) {
        $output->writeln(
            sprintf(
                "<error>%s for the merchant %s: %s</error>",
                $message,
                $account->getUuid(),
                $exception->getMessage()
            )
        );
        $this->logger->error($message, ['account' => $account, 'exception' => $exception]);
        if ($isFailFast) {
            throw $exception;
        }
    }

    /**
     * @param AccountInterface $account
     * @param bool $failFast
     * @param OutputInterface $output
     * @return void
     * @throws \Throwable
     */
    private function updateAccount(AccountInterface $account, bool $failFast, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                "<info>Updating account %s (UUID %s)</info>",
                $account->getName(),
                $account->getUuid()
            ),
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );
        try {
            $this->updateAccount->update($account);
        } catch (\Throwable $e) {
            $this->catchException($e, $account, $output, 'Cannot update merchant account', $failFast);
        }
    }

    /**
     * @param AccountInterface $account
     * @param bool $failFast
     * @param OutputInterface $output
     * @return void
     * @throws \Throwable
     */
    private function pull(AccountInterface $account, bool $failFast, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                "<info>Pulling logs for account %s (UUID %s)</info>",
                $account->getName(),
                $account->getUuid()
            ),
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );
        try {
            $this->pullLogs->pull($account);
        } catch (\Throwable $e) {
            $this->catchException($e, $account, $output, 'Cannot pull logs', $failFast);
        }
    }

    /**
     * @param AccountInterface $account
     * @param bool $failFast
     * @param OutputInterface $output
     * @return void
     * @throws \Throwable
     */
    private function apply(AccountInterface $account, bool $failFast, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                "<info>Applying logs for account %s (UUID %s)</info>",
                $account->getName(),
                $account->getUuid()
            ),
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );
        try {
            $this->applyLogs->apply($account);
        } catch (\Throwable $e) {
            $this->catchException($e, $account, $output, 'Cannot apply logs', $failFast);
        }
    }

    /**
     * @param AccountInterface $account
     * @param bool $failFast
     * @param OutputInterface $output
     * @throws \Throwable
     */
    private function push(AccountInterface $account, bool $failFast, OutputInterface $output): void
    {
        $output->writeln(
            sprintf(
                "<info>Pushing updates for account %s (UUID %s)</info>",
                $account->getName(),
                $account->getUuid()
            ),
            OutputInterface::VERBOSITY_VERY_VERBOSE
        );
        try {
            $this->pushUpdates->push($account);
        } catch (\Throwable $e) {
            $this->catchException($e, $account, $output, 'Cannot push updates', $failFast);
        }
    }

    /**
     * @param array|null $merchantUUIDs
     * @return array
     */
    private function getMatchingMerchantAccounts(?array $merchantUUIDs): array
    {
        /** @var AccountInterface[] $accountsToProcess */
        $accountsToProcess = [];
        foreach ($this->accountRepository->getActiveAccounts() as $account) {
            if ($merchantUUIDs === null || isset($merchantUUIDs[$account->getUuid()])) {
                $accountsToProcess[$account->getUuid()] = $account;
            }
        }
        return $accountsToProcess;
    }
}
