<?php
namespace Aheadworks\QuickOrder\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\AbstractProvider;
use Aheadworks\QuickOrder\Model\Exception\OperationException;

/**
 * Class AvailabilityChecker
 *
 * @package Aheadworks\QuickOrder\Model\Product
 */
class AvailabilityChecker
{
    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @param ProductDetailPool $productDetailPool
     */
    public function __construct(
        ProductDetailPool $productDetailPool
    ) {
        $this->productDetailPool = $productDetailPool;
    }

    /**
     * Check if product is available
     *
     * @param ProductInterface|Product $product
     * @param ProductListItemInterface $item
     * @param int|null $websiteId
     * @return bool
     * @throws OperationException
     */
    public function isAvailable(ProductInterface $product, $item, $websiteId = null)
    {
        $provider = $this->getProductProvider($product, $item);
        if ($websiteId === null) {
            $websiteId = $product->getStore()->getWebsiteId();
        }

        return $provider->isAvailableForQuickOrder($websiteId);
    }

    /**
     * Check if product is available for sale
     *
     * @param ProductInterface|Product $product
     * @param ProductListItemInterface $item
     * @param int|null $websiteId
     * @return bool
     * @throws OperationException
     */
    public function isAvailableForSale(ProductInterface $product, $item, $websiteId = null)
    {
        $provider = $this->getProductProvider($product, $item);
        $qty = $item->getProductQty() ?? 1;

        if ($websiteId === null) {
            $websiteId = $product->getStore()->getWebsiteId();
        }

        return $provider->isAvailableForQuickOrder($websiteId) && !$provider->getQtySalableMessage($qty);
    }

    /**
     * Get product provider
     *
     * @param ProductInterface $product
     * @param ProductListItemInterface $item
     * @return AbstractProvider
     * @throws OperationException
     */
    private function getProductProvider(ProductInterface $product, $item)
    {
        $productId = $item->getProductId() ?? $product->getId();
        $itemData = [
            ProductListItemInterface::ITEM_KEY => $item->getItemKey(),
            ProductListItemInterface::PRODUCT_ID => $productId,
            ProductListItemInterface::PRODUCT_OPTION => $item->getProductOption()
        ];

        return $this->productDetailPool->get($itemData);
    }
}
