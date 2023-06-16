<?php
namespace Aheadworks\RequisitionLists\Model\Product\DetailProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\ImageFactory as ProductImageFactory;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\RequisitionLists\Model\Product\Checker\Inventory\Checker as InventoryChecker;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;

/**
 * Class AbstractProvider
 *
 * @package Aheadworks\RequisitionLists\Model\Product\DetailProvider
 */
abstract class AbstractProvider
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var ProductInterface
     */
    protected $parentProduct;

    /**
     * @var array
     */
    protected $productAttributes;

    /**
     * @var RequisitionListItemRepositoryInterface
     */
    protected $requisitionListItemRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductImageFactory
     */
    protected $productImageFactory;

    /**
     * @var OptionConverter
     */
    protected $optionConverter;

    /**
     * @var InventoryChecker
     */
    protected $inventoryChecker;

    /**
     * @var ProductInterface[]|Product[]
     */
    protected $subProducts;

    /**
     * @var bool
     */
    private $isError;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ProductImageFactory $productImageFactory
     * @param OptionConverter $optionConverter
     * @param InventoryChecker $inventoryChecker
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductImageFactory $productImageFactory,
        OptionConverter $optionConverter,
        InventoryChecker $inventoryChecker
    ) {
        $this->storeManager = $storeManager;
        $this->productImageFactory = $productImageFactory;
        $this->optionConverter = $optionConverter;
        $this->inventoryChecker = $inventoryChecker;
    }

    /**
     * Get is error flag
     *
     * @return bool
     */
    public function getIsError()
    {
        return $this->isError;
    }

    /**
     * Set is error flag
     *
     * @param bool $isError
     */
    public function setIsError($isError)
    {
        $this->isError = $isError;
    }

    /**
     * Get product attributes
     *
     * @param ProductOptionInterface $productOption
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getProductAttributes($productOption)
    {
        $attributes = [];
        if (!$this->getIsError()) {
            $product = $this->parentProduct ?? $this->product;
            /** @var AbstractType $type */
            $type = $product->getTypeInstance();
            $orderOptions = $type->getOrderOptions($product);
            $attributes['custom_options'] = $orderOptions['options'] ?? []; //todo render custom options
            $productOptions = $this->getProductTypeAttributes($orderOptions);
            $attributes['product_options'] = !empty($productOptions) ? $productOptions : [];
        }

        return $attributes;
    }

    /**
     * Get final price for buy request
     *
     * This price is returned only in case buy request is specified
     *
     * @return string
     */
    public function getFinalPriceForBuyRequest()
    {
        $price = null;
        $orderOptions = $this->getOrderOptions();

        return empty($orderOptions['info_buyRequest'])
            ? $price
            : $this->product->getFinalPrice();
    }

    /**
     * Get order options
     *
     * @return array
     */
    protected function getOrderOptions()
    {
        $options['info_buyRequest'] = [];
        if (!$this->getIsError()) {
            $type = $this->product->getTypeInstance();
            $options = $type->getOrderOptions($this->product);
        }

        return $options;
    }

    /**
     * Get product for checking
     *
     * @return ProductInterface|Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product for check
     *
     * @param ProductInterface $product
     * @param array|null $listItemData
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setProduct($product, $listItemData = null)
    {
        $this->product = $product;
    }

    /**
     * Set parent product for check
     *
     * @param ProductInterface $parentProduct
     */
    public function setParentProduct($parentProduct)
    {
        $this->parentProduct = $parentProduct;
    }

    /**
     * Check the scope of the product usage on specific sites
     *
     * @param ProductInterface $product
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isProductInWebsite($product)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        return in_array($websiteId, $product->getWebsiteIds());
    }

    /**
     * Retrieve prepared product image html
     *
     * @return string
     */
    public function getProductImageHtml()
    {
        $productImageBlock = $this->productImageFactory->create(
            $this->product,
            'product_page_image_small',
            []
        );

        return $productImageBlock->toHtml();
    }

    /**
     * Is Product Available For Site
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isProductAvailableForSite()
    {
        return $this->product->isVisibleInSiteVisibility() && $this->isProductInWebsite($this->product);
    }

    /**
     * Get quantity is salable
     *
     * @param float|null $requestedQty
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getQtyIsSalable($requestedQty = null)
    {
        return true;
    }

    /**
     * Is qty field enabled
     *
     * @return bool
     */
    public function isQtyEnabled()
    {
        return $this->isSalable();
    }

    /**
     * Resolve and set sub products of product instance
     *
     * @param ProductInterface[] $products
     */
    public function resolveSubProducts($products)
    {
        $subProducts = [];
        foreach ($products as $product) {
            if ($product->getParentProductId()) {
                $subProducts[] = $product;
            }
        }

        $this->subProducts = $subProducts;
    }

    /**
     * Get salable result for product
     *
     * @param Product|ProductInterface $product
     * @param float|int|null $requestedQty
     * @return bool
     */
    protected function getSalableResultForProduct($product, $requestedQty = null)
    {
        return $this->inventoryChecker->isProductInStock($product, $requestedQty);
    }

    /**
     * Prepare product URL
     *
     * @param null|int $qty
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductUrl($qty = null)
    {
        $url = '';

        if ($this->isAvailableForSite() && $this->inventoryChecker->isProductVisible($this->product, $qty)) {
            $url = $this->product->getProductUrl();
        }

        return $url;
    }

    /**
     * Check product availability in site
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    abstract public function isAvailableForSite();

    /**
     * Is disabled
     *
     * @return bool
     */
    abstract public function isDisabled();

    /**
     * Is editable
     *
     * @return bool
     */
    abstract public function isEditable();

    /**
     * Is salable
     *
     * @return bool
     */
    abstract public function isSalable();

    /**
     * Get product attributes specific for product type
     *
     * @param array $orderOptions
     * @return array
     */
    abstract protected function getProductTypeAttributes($orderOptions);
}
