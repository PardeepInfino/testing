<?php

namespace Magento\Amazon\Cron;

use Magento\Amazon\Comm\Amazon\ApplyLogs;
use Magento\Amazon\Comm\Amazon\PullLogs;
use Magento\Amazon\Comm\Amazon\PushUpdates;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\AccountRepository;

class SyncCron
{
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
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var \Magento\Amazon\Model\ConfigManagement
     */
    private $configManagement;
    /**
     * @var \Magento\Framework\Lock\LockManagerInterface
     */
    private $lockManager;

    public function __construct(
        AccountRepository $accountRepository,
        PullLogs $pullLogs,
        ApplyLogs $applyLogs,
        PushUpdates $pushUpdates,
        AscClientLogger $logger,
        \Magento\Amazon\Model\ConfigManagement $configManagement,
        \Magento\Framework\Lock\LockManagerInterface $lockManager
    ) {
        $this->accountRepository = $accountRepository;
        $this->pullLogs = $pullLogs;
        $this->applyLogs = $applyLogs;
        $this->pushUpdates = $pushUpdates;
        $this->logger = $logger;
        $this->configManagement = $configManagement;
        $this->lockManager = $lockManager;
    }

    public function execute()
    {
        $useCronForSync = 1 === (int)$this->configManagement->getCronSourceSetting();
        if (!$useCronForSync) {
            $this->logger->debug('The current configuration disables running sync with cron.');
            return;
        }
        if ($this->lockManager->isLocked(\Magento\Amazon\Console\Cron\Amazon\Run::LOCK_NAME)) {
            $this->logger->info('Cannot run sync in cron job due to a lock set by running CLI command.');
            return;
        }

        $accounts = $this->accountRepository->getActiveAccounts();
        foreach ($accounts as $account) {
            $start = microtime(true);
            $this->syncAccount($account);
            $end = microtime(true);
            $syncTime = $end - $start;
            $this->logger->debug(
                'The sync finished for the account ' . $account->getName(),
                [
                    'time_to_complete' => $syncTime,
                    'account' => $account
                ]
            );
        }
    }

    /**
     * @param \Magento\Amazon\Api\Data\AccountInterface $account
     */
    private function syncAccount(\Magento\Amazon\Api\Data\AccountInterface $account): void
    {
        try {
            $this->pullLogs->pull($account);
        } catch (\Throwable $e) {
            $this->logger->critical(
                'An error occurred when pulling new logs: ' . $e->getMessage(),
                ['exception' => $e, 'account' => $account]
            );
        }
        try {
            $this->applyLogs->apply($account);
        } catch (\Throwable $e) {
            $this->logger->critical(
                'An error occurred when applying changes: ' . $e->getMessage(),
                ['exception' => $e, 'account' => $account]
            );
        }
        try {
            $this->pushUpdates->push($account);
        } catch (\Throwable $e) {
            $this->logger->critical(
                'An error occurred when pushing updates: ' . $e->getMessage(),
                ['exception' => $e, 'account' => $account]
            );
        }
    }
}
