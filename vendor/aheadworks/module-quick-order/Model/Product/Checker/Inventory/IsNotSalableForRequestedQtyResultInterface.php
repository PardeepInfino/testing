<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface IsNotSalableForRequestedQtyResultInterface
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
interface IsNotSalableForRequestedQtyResultInterface
{
    /**
     * Get result message in case product is not salable for requested qty
     *
     * @param ProductInterface $product
     * @param int|float $requestedQty
     * @return string
     */
    public function getResultMessage($product, $requestedQty);
}
