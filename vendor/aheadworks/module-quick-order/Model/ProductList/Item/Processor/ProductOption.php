<?php
namespace Aheadworks\QuickOrder\Model\ProductList\Item\Processor;

use Aheadworks\QuickOrder\Model\ProductList\Item\ProcessorInterface;
use Aheadworks\QuickOrder\Model\Product\Option\Converter as OptionConverter;

/**
 * Class ProductOption
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item\Processor
 */
class ProductOption implements ProcessorInterface
{
    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @param OptionConverter $optionConverter
     */
    public function __construct(
        OptionConverter $optionConverter
    ) {
        $this->optionConverter = $optionConverter;
    }

    /**
     * @inheritdoc
     */
    public function process($requestItem, $item, $product)
    {
        if (!$requestItem->getProductOption()) {
            if (!$item->getProductOption()) {
                $option = $this->optionConverter->toProductOptionObject($product->getTypeId(), []);
                $item->setProductOption($option);
            }
        } else {
            $item->setProductOption($requestItem->getProductOption());
        }
    }
}
