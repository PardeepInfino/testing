<?php
namespace Aheadworks\RequisitionLists\Model\Product\Checker\Inventory;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface IsProductSalableResultInterface
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
interface IsProductSalableResultInterface
{
    /**
     * Check if product is salable for requested qty
     *
     * @param ProductInterface $product
     * @param int|float $requestedQty
     * @return bool
     */
    public function isSalable($product, $requestedQty);
}
