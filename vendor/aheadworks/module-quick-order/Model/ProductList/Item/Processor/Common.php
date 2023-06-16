<?php
namespace Aheadworks\QuickOrder\Model\ProductList\Item\Processor;

use Aheadworks\QuickOrder\Model\ProductList\Item\ProcessorInterface;

/**
 * Class Common
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item\Processor
 */
class Common implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process($requestItem, $item, $product)
    {
        if (!$item->getItemKey()) {
            $item->setItemKey(uniqid('', false));
        }
        $item->setProductId($product->getId());
        $item->setProductName($product->getName());
        $item->setProductSku($product->getSku());
        $item->setProductType($product->getTypeId());
    }
}
