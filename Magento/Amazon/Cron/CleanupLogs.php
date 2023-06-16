<?php

namespace Magento\Amazon\Cron;

use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ConfigManagement;
use Magento\Amazon\Model\ResourceModel\Amazon\Error\Log as ErrorLogResource;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as ListingLogResource;
use Magento\Framework\Exception\LocalizedException;

class CleanupLogs
{
    private const CLEAR_LOGS_INTERVAL_DAYS_DEFAULT = 7;

    /**
     * @var ListingLogResource
     */
    private $listingLogResource;
    /**
     * @var ErrorLogResource
     */
    private $errorLogResource;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var ConfigManagement
     */
    private $configManagement;

    /**
     * @param ListingLogResource $listingLogResource
     * @param ErrorLogResource $errorLogResource
     * @param AscClientLogger $logger
     * @param ConfigManagement $configManagement
     */
    public function __construct(
        ListingLogResource $listingLogResource,
        ErrorLogResource $errorLogResource,
        AscClientLogger $logger,
        ConfigManagement $configManagement
    ) {
        $this->listingLogResource = $listingLogResource;
        $this->errorLogResource = $errorLogResource;
        $this->logger = $logger;
        $this->configManagement = $configManagement;
    }

    public function execute()
    {
        $logHistoryDays = $this->getLogHistoryDays();
        try {
            $this->listingLogResource->clearLogs($logHistoryDays);
            $this->errorLogResource->clearLogs($logHistoryDays);
        } catch (LocalizedException $e) {
            $this->logger->error('Exception during logs cleanup: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Returns user set interval to clear logs (in days)
     *
     * @return int
     */
    private function getLogHistoryDays(): int
    {
        $this->logger->debug('Returns user set interval to clear logs.');
        $logHistoryDays = (int)$this->configManagement->getLogHistorySetting();
        if (1 <= $logHistoryDays) {
            return $logHistoryDays;
        }

        return self::CLEAR_LOGS_INTERVAL_DAYS_DEFAULT;
    }
}
