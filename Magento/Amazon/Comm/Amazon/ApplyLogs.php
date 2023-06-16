<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Cache\StoresWithOrdersThatCannotBeImported;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\AccountManagement;
use Magento\Amazon\Model\Amazon\ListingManagement;
use Magento\Amazon\Model\ConfigManagement;
use Magento\Amazon\Model\Logs\LogsResource;
use Magento\Amazon\Model\LogStateManagement;
use Magento\Amazon\Model\Order\OrderHandlerResolver;
use Magento\Amazon\Model\ResourceModel\Amazon\Error\Log as ErrorLogResource;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ListingResource;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as ListingLogResource;
use Magento\Framework\Exception\LocalizedException;

class ApplyLogs
{
    /**
     * @var LogsResource
     */
    private $logsResource;
    /**
     * @var LogStateManagement
     */
    private $logStateManagement;
    /**
     * @var UpdateHandler
     */
    private $updateHandler;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var AccountManagement
     */
    private $accountManagement;
    /**
     * @var ListingManagement
     */
    private $listingManagement;
    /**
     * @var OrderHandlerResolver
     */
    private $orderHandlerResolver;
    /**
     * @var ListingResource
     */
    private $listingResource;
    /**
     * @var ListingLogResource
     */
    private $listingLogResource;
    /**
     * @var ErrorLogResource
     */
    private $errorLogResource;
    /**
     * @var ConfigManagement
     */
    private $configManagement;
    /**
     * @var StoresWithOrdersThatCannotBeImported
     */
    private $storesWithOrdersThatCannotBeImported;
    /**
     * @var int
     */
    private $batchSize;

    /**
     * ApplyUpdates constructor.
     * @param LogsResource $logsResource
     * @param LogStateManagement $logStateManagement
     * @param UpdateHandler $updateHandler
     * @param AscClientLogger $logger
     * @param AccountManagement $accountManagement
     * @param ListingManagement $listingManagement
     * @param OrderHandlerResolver $orderHandlerResolver
     * @param ListingResource $listingResource
     * @param ListingLogResource $listingLogResource
     * @param ErrorLogResource $errorLogResource
     * @param ConfigManagement $configManagement
     * @param StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported
     * @param int $batchSize
     */
    public function __construct(
        LogsResource $logsResource,
        LogStateManagement $logStateManagement,
        UpdateHandler $updateHandler,
        AscClientLogger $logger,
        AccountManagement $accountManagement,
        ListingManagement $listingManagement,
        OrderHandlerResolver $orderHandlerResolver,
        ListingResource $listingResource,
        ListingLogResource $listingLogResource,
        ErrorLogResource $errorLogResource,
        ConfigManagement $configManagement,
        StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported,
        int $batchSize = 1000
    ) {
        $this->logsResource = $logsResource;
        $this->logStateManagement = $logStateManagement;
        $this->updateHandler = $updateHandler;
        $this->logger = $logger;
        $this->accountManagement = $accountManagement;
        $this->listingManagement = $listingManagement;
        $this->orderHandlerResolver = $orderHandlerResolver;
        $this->listingResource = $listingResource;
        $this->listingLogResource = $listingLogResource;
        $this->errorLogResource = $errorLogResource;
        $this->configManagement = $configManagement;
        $this->storesWithOrdersThatCannotBeImported = $storesWithOrdersThatCannotBeImported;
        $this->batchSize = $batchSize;
    }

    public function apply(AccountInterface $account)
    {
        $this->storesWithOrdersThatCannotBeImported->remove($account);
        try {
            $this->processLogs($account);
            $this->scheduleActionsForMerchant($account);
        } finally {
            $this->storesWithOrdersThatCannotBeImported->persist();
        }
    }

    /**
     * @param $account
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function scheduleActionsForMerchant(AccountInterface $account): void
    {
        $accountReadyToPushCommands = $this->accountManagement->isAccountReadyToPushCommands($account);

        if ($accountReadyToPushCommands) {
            $merchantId = (int)$account->getMerchantId();
            $orderHandler = $this->orderHandlerResolver->resolve();

            // process scheduled listing additions (if set to automatically list) and removals
            $this->listingManagement->scheduleListingInsertions($merchantId);
            $this->listingResource->scheduleListingRemovals($merchantId);
            $this->listingResource->scheduleConditionOverrides($merchantId);
            $orderHandler->synchronizeOrders($merchantId);
        }
    }

    private function processLogs(AccountInterface $account): void
    {
        $lastId = 0;

        do {
            $processedLogs = [];
            $logs = $this->logsResource->getLogs($account, null, null, $lastId, $this->batchSize);
            if (!$logs) {
                return;
            }
            try {
                $this->logsResource->beginTransaction();
                $items = $this->getLogsAvailableForProcessing($logs);
                $this->logger->debug(
                    'Filtered In Progress logs',
                    [
                        'logs_fetched' => count($logs),
                        'logs_left' => count($items),
                    ]
                );
                $this->logStateManagement->processing(array_keys($items));
                $processedLogs = $this->updateHandler->handle($logs, $account);
                if ($processedLogs) {
                    $this->logger->info(
                        'Deleting processed logs',
                        ['count' => count($processedLogs), 'debug' => ['logIds' => $processedLogs]]
                    );
                    $this->logsResource->deleteByIds($account, $processedLogs);
                }
                $this->logsResource->commit();
            } catch (\Exception $e) {
                $this->logger->critical(
                    'Exception occurred during logs processing',
                    ['exception' => $e, 'account' => $account]
                );
                $this->logsResource->rollBack();
            } finally {
                $this->logStateManagement->complete($processedLogs);
            }
            // todo: should be just array_key_last($logs) once we'll drop PHP <= 7.3.0
            $lastId = key(array_slice($logs, -1, 1, true));
        } while (count($logs) === $this->batchSize);
    }

    /**
     * @param $logs
     * @return array
     * @throws LocalizedException
     */
    private function getLogsAvailableForProcessing($logs): array
    {
        $logIds = array_column($logs, 'id');
        $items = array_combine($logIds, $logs);
        $processableLogs = array_flip($this->logStateManagement->filterProcessableLogs($logIds));
        $items = array_intersect_key($items, $processableLogs);
        return $items;
    }
}
