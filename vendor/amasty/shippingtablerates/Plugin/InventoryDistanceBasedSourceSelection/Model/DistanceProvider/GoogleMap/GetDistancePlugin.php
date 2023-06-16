<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shipping Table Rates for Magento 2
 */

namespace Amasty\ShippingTableRates\Plugin\InventoryDistanceBasedSourceSelection\Model\DistanceProvider\GoogleMap;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryDistanceBasedSourceSelection\Model\DistanceProvider\GoogleMap\GetDistance;
use Magento\InventoryDistanceBasedSourceSelection\Model\DistanceProvider\Offline\GetDistance as OfflineGetDistance;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterface;

class GetDistancePlugin
{
    /**
     * @var OfflineGetDistance
     */
    private $offlineGetDistance;

    public function __construct(
        OfflineGetDistance $offlineGetDistance
    ) {
        $this->offlineGetDistance = $offlineGetDistance;
    }

    public function aroundExecute(
        GetDistance $subject,
        \Closure $proceed,
        LatLngInterface $source,
        LatLngInterface $destination
    ): float {
        try {
            return $proceed($source, $destination);
        } catch (LocalizedException $e) {
            // Use offline calculation if a request to the Google Maps Distance Matrix Api return ZERO_RESULTS.
            return $this->offlineGetDistance->execute($source, $destination);
        }
    }
}
