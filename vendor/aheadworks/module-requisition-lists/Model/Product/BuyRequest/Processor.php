<?php
namespace Aheadworks\RequisitionLists\Model\Product\BuyRequest;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\RequisitionLists\Model\Product\BuyRequest
 */
class Processor
{
    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param OptionConverter $optionConverter
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        OptionConverter $optionConverter,
        ProductRepositoryInterface $productRepository
    ) {
        $this->optionConverter = $optionConverter;
        $this->productRepository = $productRepository;
    }

    /**
     * Prepare buy request using product list item
     *
     * @param RequisitionListItemInterface $item
     * @return DataObject
     */
    public function prepareBuyRequest($item)
    {
        $buyRequest = $this->optionConverter->toBuyRequest($item->getProductType(), $item->getProductOption());
        $buyRequest->addData(['qty' => $item->getProductQty()]);

        return $buyRequest;
    }

    /**
     * Prepare buy request data for quote item
     *
     * @param QuoteItem $quoteItem
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareForQuoteItem($quoteItem)
    {
        $params = $quoteItem->getBuyRequest()->getData();
        $params[RequisitionListItemInterface::PRODUCT_ID] = $quoteItem->getProductId();

        return $params;
    }
}
