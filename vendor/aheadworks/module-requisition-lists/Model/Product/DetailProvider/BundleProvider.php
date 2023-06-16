<?php
namespace Aheadworks\RequisitionLists\Model\Product\DetailProvider;

/**
 * Class BundleProvider
 *
 * @package Aheadworks\RequisitionLists\Model\Product\DetailProvider
 */
class BundleProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['bundle_options']) ? array_values($orderOptions['bundle_options']) : [];
    }

    /**
     * @inheritdoc
     */
    public function resolveSubProducts($products)
    {
        parent::resolveSubProducts($products);
        if (count($this->subProducts)) {
            $this->parentProduct = $this->product;
        }
    }

    /**
     * @inheritdoc
     */
    public function getProductUrl($qty = null)
    {
        $url = '';

        $product = null;
        if (!is_array($this->subProducts) && !$this->parentProduct) {
            $product = $this->product;
        } elseif ($this->parentProduct) {
            $product = $this->parentProduct;
        }

        if ($product && $this->inventoryChecker->isParentProductVisible($product)) {
            return $product->getProductUrl();
        }

        return $url;
    }

    /**
     * @inheritdoc
     */
    public function getQtyIsSalable($requestedQty = null)
    {
        $result = true;

        if ($this->getIsError()) {
            return false;
        }

        foreach ($this->subProducts as $product) {
            if (!$this->getSalableResultForProduct($product, $requestedQty)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isAvailableForSite()
    {
        return $this->product->isVisibleInSiteVisibility() && $this->isProductInWebsite($this->product);
    }

    /**
     * @inheritDoc
     */
    public function isDisabled()
    {
        return $this->product->isDisabled();
    }

    /**
     * @inheritDoc
     */
    public function isEditable()
    {
        return $this->product->getTypeInstance()->hasRequiredOptions($this->product);
    }

    /**
     * @inheritDoc
     */
    public function isQtyEnabled()
    {
        $result = true;
        if ($this->getIsError()) {
            return false;
        }
        foreach ($this->subProducts as $product) {
            if (!$product->isSalable()) {
                $result = false;
            }
        }

        return $this->isSalable() && $result;
    }

    /**
     * @inheritDoc
     */
    public function isSalable()
    {
        return $this->product->isSalable();
    }
}
