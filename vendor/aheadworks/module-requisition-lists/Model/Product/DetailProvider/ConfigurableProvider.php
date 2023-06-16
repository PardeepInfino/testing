<?php
namespace Aheadworks\RequisitionLists\Model\Product\DetailProvider;

use Aheadworks\RequisitionLists\Model\Config;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Magento\Catalog\Block\Product\ImageFactory as ProductImageFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\RequisitionLists\Model\Product\Checker\Inventory\Checker as InventoryChecker;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigurableProvider
 *
 * @package Aheadworks\RequisitionLists\Model\Product\DetailProvider
 */
class ConfigurableProvider extends AbstractProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ProductImageFactory $productImageFactory
     * @param OptionConverter $optionConverter
     * @param InventoryChecker $inventoryChecker
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductImageFactory $productImageFactory,
        OptionConverter $optionConverter,
        InventoryChecker $inventoryChecker,
        Config $config
    ) {
        parent::__construct(
            $storeManager,
            $productImageFactory,
            $optionConverter,
            $inventoryChecker
        );
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function resolveSubProducts($products)
    {
        parent::resolveSubProducts($products);
        if (count($this->subProducts)) {
            $this->parentProduct = $this->product;
            $this->product = reset($this->subProducts);
        }
    }

    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['attributes_info']) ? array_values($orderOptions['attributes_info']) : [];
    }

    /**
     * @inheritdoc
     */
    public function getProductUrl($qty = null)
    {
        $url = '';

        if ($this->parentProduct && !$this->inventoryChecker->isParentProductVisible($this->parentProduct)) {
            return $url;
        }

        if (($this->inventoryChecker->isProductVisible($this->product, $qty) && $this->isAvailableForSite())
            || ($this->parentProduct
            && $this->parentProduct->isVisibleInSiteVisibility()
            && !$this->parentProduct->isDisabled())
        ) {
            $url = $this->isProductAvailableForSite() ?
                $this->product->getProductUrl():
                $this->parentProduct->getProductUrl();
        }

        return $url;
    }

    /**
     * @inheritDoc
     */
    public function isAvailableForSite()
    {
        return $this->isProductAvailableForSite() || $this->isParentProductAvailableForSite();
    }

    /**
     * @inheritDoc
     */
    public function isDisabled()
    {
        return $this->product->isDisabled() || ($this->parentProduct && $this->parentProduct->isDisabled());
    }

    /**
     * @inheritDoc
     */
    public function isEditable()
    {
        return $this->isParentAvailableForSale();
    }

    /**
     * @inheritDoc
     */
    public function isSalable()
    {
        return $this->product->isSalable() || ($this->parentProduct && $this->parentProduct->isSalable());
    }

    /**
     * @inheritDoc
     */
    public function isQtyEnabled()
    {
        return $this->product->isSalable() && $this->parentProduct && $this->parentProduct->isSalable();
    }

    /**
     * Retrieve prepared product image html
     *
     * @return string
     * @throws LocalizedException
     */
    public function getProductImageHtml()
    {
        if ($this->config->isUseParentImageForConfigurable($this->storeManager->getStore()->getId())
            && $this->parentProduct
        ) {
            $product = $this->parentProduct;
        } else {
            $product = $this->product;
        }

        $productImageBlock = $this->productImageFactory->create(
            $product,
            'product_page_image_small',
            []
        );

        return $productImageBlock->toHtml();
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
            : $this->parentProduct->getFinalPrice();
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
            $type = $this->parentProduct->getTypeInstance();
            $options = $type->getOrderOptions($this->parentProduct);
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    public function getQtyIsSalable($requestedQty = null)
    {
        $product = $this->parentProduct ? $this->product : null;
        if (!$product) {
            return false;
        }

        return $this->getSalableResultForProduct($product, $requestedQty);
    }

    /**
     * @inheritdoc
     */
    private function isParentProductAvailableForSite()
    {
        return $this->parentProduct && $this->isProductInWebsite($this->parentProduct);
    }

    /**
     * Check if parent product are enabled and in stock
     *
     * @return bool
     */
    private function isParentAvailableForSale()
    {
        if ($this->parentProduct && ($this->parentProduct->isDisabled() || !$this->parentProduct->isSalable())) {
            return false;
        }

        return true;
    }
}
