<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shipping Table Rates for Magento 2
 */

namespace Amasty\ShippingTableRates\Plugin\InventoryDistanceBasedSourceSelection\Model\Convert;

use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\AddressToQueryString;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterface;

class AddressToQueryStringPlugin
{
    public function afterExecute(AddressToQueryString $subject, $result, AddressInterface $address): string
    {
        if ($region = $address->getRegion()) {
            $result .= ', ' . $region;
        }

        return $result;
    }
}
