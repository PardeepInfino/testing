<?php
namespace Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Relation\Item;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item as ItemSource;
use Aheadworks\QuickOrder\Model\Product\Option\Serializer as OptionSerializer;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Relation\Item
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var OptionSerializer
     */
    private $optionSerializer;

    /**
     * @var string
     */
    private $productListItemTable;

    /**
     * @param MetadataPool $metadataPool
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ResourceConnection $resourceConnection
     * @param OptionSerializer $optionSerializer
     */
    public function __construct(
        MetadataPool $metadataPool,
        DataObjectProcessor $dataObjectProcessor,
        ResourceConnection $resourceConnection,
        OptionSerializer $optionSerializer
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->optionSerializer = $optionSerializer;

        $this->productListItemTable = $this->resourceConnection->getTableName(ItemSource::MAIN_TABLE_NAME);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var ProductListInterface $entity */
        $listId = (int)$entity->getListId();
        if (!$listId || $entity->getItems() === null) {
            return $entity;
        }

        $itemsToUpdate = [];
        $itemsToInsert = [];
        foreach ($entity->getItems() as $item) {
            $itemData = $this->dataObjectProcessor->buildOutputDataArray(
                $item,
                ProductListItemInterface::class
            );

            $productOption = $this->optionSerializer->serializeOptionArrayToString(
                $itemData[ProductListItemInterface::PRODUCT_OPTION]
            );
            $itemData[ProductListItemInterface::LIST_ID] = $listId;
            $itemData[ProductListItemInterface::PRODUCT_OPTION] = $productOption;

            if ($item->getItemId()) {
                $itemsToUpdate[] = $itemData;
            } else {
                $itemsToInsert[] = $itemData;
            }
        }

        $itemIds = array_column($itemsToUpdate, ProductListItemInterface::ITEM_ID);
        $this->deleteUnusedItems($listId, $itemIds);
        $this->updateItems($itemsToUpdate);
        $this->insertItems($itemsToInsert);

        return $entity;
    }

    /**
     * Remove unused items
     *
     * @param int $listId
     * @param array $itemIds
     * @return int
     * @throws \Exception
     */
    private function deleteUnusedItems($listId, $itemIds)
    {
        $connection = $this->getConnection();
        $whereCondition[] = $connection->quoteInto(ProductListItemInterface::LIST_ID . ' = ?', $listId);
        if (!empty($itemIds)) {
            $whereCondition[] = $connection->quoteInto(ProductListItemInterface::ITEM_ID . ' NOT IN (?)', $itemIds);
        }

        return $connection->delete(
            $this->productListItemTable,
            $whereCondition
        );
    }

    /**
     * Update product list items
     *
     * @param array $items
     * @return $this
     * @throws \Exception
     */
    private function updateItems($items)
    {
        if (!empty($items)) {
            $connection = $this->getConnection();
            foreach ($items as $item) {
                $where = $connection->quoteInto(
                    ProductListItemInterface::ITEM_ID . ' = ?',
                    $item[ProductListItemInterface::ITEM_ID]
                );
                $connection->update($this->productListItemTable, $item, $where);
            }
        }
        return $this;
    }

    /**
     * Insert new product list items
     *
     * @param array $items
     * @return $this
     * @throws \Exception
     */
    private function insertItems($items)
    {
        if (!empty($items)) {
            $this->getConnection()->insertMultiple($this->productListItemTable, $items);
        }
        return $this;
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(ProductListInterface::class)->getEntityConnectionName()
        );
    }
}
