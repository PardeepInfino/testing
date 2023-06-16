<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Elastic Search Base for Magento 2
 */

namespace Amasty\ElasticSearch\Model\Indexer\Data\Product;

use Amasty\ElasticSearch\Api\Data\Indexer\Data\DataMapperInterface;
use Amasty\ElasticSearch\Model\Di\Wrapper as DiWrapper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Provide is_out_of_stock value.
 * Need because we have our data mapper, which different with magento data mapper.
 *
 * @see \Magento\InventoryElasticsearch\Plugin\Model\Adapter\BatchDataMapper\ProductDataMapperPlugin
 */
class OutofStockDataMapper implements DataMapperInterface
{
    public const OUT_OF_STOCK_FIELD = 'is_out_of_stock';

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var DiWrapper|StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * @var DiWrapper|GetStockItemDataInterface
     */
    private $getStockItemData;

    /**
     * @var AttributeDataProvider
     */
    private $attributeDataProvider;

    /**
     * @var int|null
     */
    private $skuAttributeId;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        DiWrapper $stockByWebsiteIdResolver,
        DiWrapper $getStockItemData,
        AttributeDataProvider $attributeDataProvider
    ) {
        $this->storeRepository = $storeRepository;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->getStockItemData = $getStockItemData;
        $this->attributeDataProvider = $attributeDataProvider;
    }

    /**
     * @param array $indexData
     * @param int $storeId
     * @param array $context
     * @return array
     */
    public function map(array $indexData, $storeId, array $context = [])
    {
        $store = $this->storeRepository->getById($storeId);
        $stock = $this->stockByWebsiteIdResolver->execute((int) $store->getWebsiteId());
        if ($stock === false) {
            return [];
        }

        $outOfStockValues = [];
        foreach ($indexData as $productId => $document) {
            $sku = $this->getSku($document);
            if (!$sku) {
                $isOutOfStock = 1;
            } else {
                try {
                    $stockItemData = $this->getStockItemData->execute($sku, $stock->getStockId());
                } catch (NoSuchEntityException $e) {
                    $stockItemData = null;
                }
                $isOutOfStock = null !== $stockItemData
                    ? (int) $stockItemData[GetStockItemDataInterface::IS_SALABLE] : 1;
            }
            $outOfStockValues[$productId] = [self::OUT_OF_STOCK_FIELD => $isOutOfStock];
        }

        return $outOfStockValues;
    }

    private function getSku(array $document): string
    {
        if ($this->skuAttributeId === null) {
            $this->resolveSkuAttributeId($document);
        }

        return $document[$this->skuAttributeId] ?? '';
    }

    private function resolveSkuAttributeId(array $document): void
    {
        foreach ($document as $attributeId => $value) {
            if ($this->attributeDataProvider->getAttribute($attributeId)->getAttributeCode() === 'sku') {
                $this->skuAttributeId = $attributeId;
                break;
            }
        }
    }
}