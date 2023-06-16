<?php

namespace Magento\Amazon\Cron;

use Magento\Amazon\Comm\Amazon\UpdateAccount;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\AccountRepository;

/**
 * Cron job to update account details from the SaaS to keep it in sync with potential account changes
 * caused by another Magento instances connected to the same account
 */
class UpdateAccountFromSaaS
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;
    /**
     * @var UpdateAccount
     */
    private $updateAccount;
    /**
     * @var AscClientLogger
     */
    private $logger;

    public function __construct(
        AccountRepository $accountRepository,
        UpdateAccount $updateAccount,
        AscClientLogger $logger
    ) {
        $this->accountRepository = $accountRepository;
        $this->updateAccount = $updateAccount;
        $this->logger = $logger;
    }

    public function execute()
    {
        foreach ($this->accountRepository->getActiveAccountsIncludingNotAuthenticated() as $account) {
            try {
                $this->updateAccount->update($account);
            } catch (\Throwable $e) {
                $this->logger->critical(
                    'An error occurred when updating account from SaaS: ' . $e->getMessage(),
                    ['exception' => $e, 'account' => $account]
                );
            }
        }
    }
}
