<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Model\Logs\LogsResource;
use Magento\Amazon\Model\LogStateManagement;
use Magento\Amazon\Model\SyncStatus\SyncStatusResource;

class PullLogs
{
    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var LogStateManagement
     */
    private $logStateManagement;
    /**
     * @var LogsResource
     */
    private $logsResource;
    /**
     * @var SyncStatusResource
     */
    private $syncStatusResource;

    /**
     * @param ApiClient $apiClient
     * @param AscClientLogger $logger
     * @param LogStateManagement $logStateManagement
     * @param LogsResource $logsResource
     * @param SyncStatusResource $syncStatusResource
     */
    public function __construct(
        ApiClient $apiClient,
        AscClientLogger $logger,
        LogStateManagement $logStateManagement,
        LogsResource $logsResource,
        SyncStatusResource $syncStatusResource
    ) {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
        $this->logStateManagement = $logStateManagement;
        $this->logsResource = $logsResource;
        $this->syncStatusResource = $syncStatusResource;
    }

    public function pull(AccountInterface $account): void
    {
        $previousRequestToken = null;
        $nextRequestToken = $this->syncStatusResource->getNextToken($account);
        try {
            do {
                $totalRowsCount = 0;
                $response = $this->apiClient->fetchLogs($account, $nextRequestToken);
                $previousRequestToken = $nextRequestToken;
                $nextRequestToken = $response['lastLogToken'] ?? null;
                $logs = $response['logs'] ?? [];
                $totalRowsCount = count($logs);

                $this->logger->debug(
                    'Pulled new updates from SaaS',
                    [
                        'logs_fetched' => $totalRowsCount,
                        'last_used_token' => $previousRequestToken,
                        'received_next_token' => $nextRequestToken,
                    ]
                );

                if ($totalRowsCount !== 0) {
                    $this->logsResource->insertLogs($account, $logs);
                    $this->syncStatusResource->recordSyncResult(
                        $account,
                        $previousRequestToken,
                        $nextRequestToken,
                        $totalRowsCount,
                        SyncStatusResource::STATUS_SUCCESS
                    );
                }
            } while ($totalRowsCount !== 0);
        } catch (\Throwable $exception) {
            $status = $exception instanceof ApiException
                ? SyncStatusResource::STATUS_API_ERROR
                : SyncStatusResource::STATUS_UNKNOWN_ERROR;
            $this->syncStatusResource->recordSyncResult(
                $account,
                $previousRequestToken,
                $nextRequestToken,
                $totalRowsCount ?? 0,
                $status,
                $exception->getMessage()
            );
            throw $exception;
        }
    }
}
