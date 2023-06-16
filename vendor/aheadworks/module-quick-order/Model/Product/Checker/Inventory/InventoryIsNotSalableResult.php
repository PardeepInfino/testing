<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;

/**
 * Class InventoryIsNotSalableResult
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
class InventoryIsNotSalableResult implements IsNotSalableForRequestedQtyResultInterface
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
    public function getResultMessage($product, $requestedQty)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $isSalableResult = $this->stockStateProvider->checkQuoteItemQty($stockItem, $requestedQty, $requestedQty);

        return $isSalableResult->getHasError() ? $isSalableResult->getMessage() : '';
    }
}
