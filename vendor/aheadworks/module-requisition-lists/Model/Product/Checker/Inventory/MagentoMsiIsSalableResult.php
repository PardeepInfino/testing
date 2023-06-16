<?php
namespace Aheadworks\RequisitionLists\Model\Product\Checker\Inventory;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

/**
 * Interface MagentoMsiIsSalableResult
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
class MagentoMsiIsSalableResult implements IsProductSalableResultInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function isSalable($product, $requestedQty)
    {
        try {
            $websiteCode = $this->storeManager->getWebsite($product->getStore()->getWebsiteId())->getCode();
            /** @var StockResolverInterface $stockResolver */
            $stockResolver = $this->objectManager->get(StockResolverInterface::class);
            $stock = $stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
            /** @var IsProductSalableForRequestedQtyInterface $isProductSalable */
            $isProductSalable = $this->objectManager->get(IsProductSalableForRequestedQtyInterface::class);
            $isSalableResult = $isProductSalable->execute($product->getSku(), $stock->getStockId(), $requestedQty);
        } catch (LocalizedException $exception) {
            return false;
        }

        if ($isSalableResult->isSalable() === false) {
            return false;
        }

        return true;
    }
}
