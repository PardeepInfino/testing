<?php
namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item
 */
interface ProcessorInterface
{
    /**
     * Prepare product list item using product and requested item
     *
     * @param ItemDataInterface $requestItem
     * @param ProductListItemInterface $item
     * @param ProductInterface $product
     */
    public function process($requestItem, $item, $product);
}
