<?php
namespace Aheadworks\RequisitionLists\Model\Product\View;

use Magento\Framework\Registry;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\Product\BuyRequest\Processor;

/**
 * Class DataApplier
 * @package Aheadworks\RequisitionLists\Model\Product\View
 */
class DataApplier
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Processor
     */
    private $buyRequestProcessor;

    /**
     * @param Registry $coreRegistry
     * @param Processor $buyRequestProcessor
     */
    public function __construct(
        Registry $coreRegistry,
        Processor $buyRequestProcessor
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->buyRequestProcessor = $buyRequestProcessor;
    }

    /**
     * Apply product data required for product view rendering
     *
     * @param ProductInterface|Product $product
     * @param RequisitionListItemInterface $item
     */
    public function apply($product, $item)
    {
        $this->coreRegistry->register('product', $product);
        $this->coreRegistry->register('current_product', $product);

        $buyRequest = $this->buyRequestProcessor->prepareBuyRequest($item);
        $optionValues = $product->processBuyRequest($buyRequest);
        $product->setPreconfiguredValues($optionValues);
        $product->setConfigureMode(true);
    }
}
