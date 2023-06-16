<?php
namespace Aheadworks\RequisitionLists\Model\Product\DetailProvider;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class SimpleProvider
 *
 * @package Aheadworks\RequisitionLists\Model\Product\DetailProvider
 */
class SimpleProvider extends AbstractProvider
{
    /**
     * @inheritDoc
     */
    public function isAvailableForSite()
    {
        if ($this->parentProduct) {
            $available = $this->parentProduct->isVisibleInSiteVisibility() && $this->isProductInWebsite($this->parentProduct);
        } else {
            $available = $this->product->isVisibleInSiteVisibility() && $this->isProductInWebsite($this->product);
        }

        return $available;
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
        return (bool)$this->product->getTypeInstance()->hasOptions($this->product);
    }

    /**
     * @inheritDoc
     */
    public function isSalable()
    {
        return $this->product->isSalable();
    }

    /**
     * @inheritdoc
     */
    public function getQtyIsSalable($requestedQty = null)
    {
        return $this->getSalableResultForProduct($this->getProduct(), $requestedQty);
    }

    /**
     * @inheritDoc
     */
    public function isQtyEnabled()
    {
        return $this->isSalable() && $this->isProductInWebsite($this->product);
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
    protected function getProductTypeAttributes($orderOptions)
    {
        return [];
    }
}
