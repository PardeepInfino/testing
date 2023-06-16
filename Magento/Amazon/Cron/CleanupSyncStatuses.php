<?php

namespace Magento\Amazon\Cron;

use Magento\Amazon\Model\Amazon\AccountRepository;
use Magento\Amazon\Model\SyncStatus\SyncStatusResource;

class CleanupSyncStatuses
{
    /**
     * @var SyncStatusResource
     */
    private $syncStatusResource;
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * CleanupSyncStatuses constructor.
     * @param SyncStatusResource $syncStatusResource
     * @param AccountRepository $accountRepository
     */
    public function __construct(SyncStatusResource $syncStatusResource, AccountRepository $accountRepository)
    {
        $this->syncStatusResource = $syncStatusResource;
        $this->accountRepository = $accountRepository;
    }

    public function execute()
    {
        $this->syncStatusResource->cleanStatuses(null, new \DateTimeImmutable('-1 week'));
    }
}
