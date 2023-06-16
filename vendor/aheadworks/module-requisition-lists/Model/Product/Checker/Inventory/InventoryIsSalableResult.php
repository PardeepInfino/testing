<?php
namespace Aheadworks\RequisitionLists\Model\Product\Checker\Inventory;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;

/**
 * Class InventoryIsSalableResult
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
class InventoryIsSalableResult implements IsProductSalableResultInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var StockStateProviderInterface
     */
    private $stockStateProvider;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateProviderInterface $stockStateProvider
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        StockStateProviderInterface $stockStateProvider
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->stockStateProvider = $stockStateProvider;
    }

    /**
     * @inheritdoc
     */
    public function isSalable($product, $requestedQty)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $isSalableResult = $this->stockStateProvider->checkQuoteItemQty($stockItem, $requestedQty, $requestedQty);

        return !$isSalableResult->getHasError();
    }
}
