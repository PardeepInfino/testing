<?php
namespace Aheadworks\QuickOrderGraphQl\Model\ProductList;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\QuickOrder\Model\Exception\OperationException;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterfaceFactory;

/**
 * Class ItemDataProcessor
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\ProductList
 */
class ItemDataProcessor
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var ProductListItemInterfaceFactory
     */
    private $productListFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param ProductDetailPool $productDetailPool
     * @param ProductListItemInterfaceFactory $productListFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        ProductDetailPool $productDetailPool,
        ProductListItemInterfaceFactory $productListFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->productDetailPool = $productDetailPool;
        $this->productListFactory = $productListFactory;
    }

    /**
     * Fill up product list item with different information
     *
     * @param array $item
     * @param int $websiteId
     * @return array
     * @throws OperationException
     */
    public function process($item, $websiteId)
    {
        /** @var ProductListItemInterface $productListItem */
        $productListItem = $this->productListFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $productListItem,
            $item,
            ProductListItemInterface::class
        );
        $item[ProductListItemInterface::PRODUCT_OPTION] = $productListItem->getProductOption();
        $provider = $this->productDetailPool->get($item);
        $options = $provider->getProductAttributes();
        if (isset($options['custom_options'])) {
            $item['custom_options'] = $options['custom_options'];
        }
        if (isset($options['product_options'])) {
            $item[$item[ProductListItemInterface::PRODUCT_TYPE] . '_options'] = $options['product_options'];
        }

        $item['product_name_url'] = $provider->getProductUrl();
        $item['preparation_error'] = $provider->getProductPreparationError();
        $item['is_available'] = $provider->isAvailableForSite($websiteId);
        $item['is_available_for_quick_order'] = $provider->isAvailableForQuickOrder($websiteId);
        $item['is_salable'] = $provider->isSalable();
        $item['is_disabled'] = $provider->isDisabled();
        $item['is_editable'] = $provider->isEditable();

       return $item;
    }
}
