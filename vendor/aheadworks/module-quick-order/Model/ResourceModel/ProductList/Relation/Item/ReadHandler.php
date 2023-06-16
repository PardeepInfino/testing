<?php
namespace Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Relation\Item;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterfaceFactory;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item as ItemSource;
use Aheadworks\QuickOrder\Model\Product\Option\Serializer as OptionSerializer;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Relation\Item
 */
class ReadHandler implements ExtensionInterface
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ProductListItemInterfaceFactory
     */
    private $productListItemFactory;

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
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param ProductListItemInterfaceFactory $productListItemFactory
     * @param OptionSerializer $optionSerializer
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        ProductListItemInterfaceFactory $productListItemFactory,
        OptionSerializer $optionSerializer
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->productListItemFactory = $productListItemFactory;
        $this->optionSerializer = $optionSerializer;
        $this->productListItemTable = $this->resourceConnection->getTableName(ItemSource::MAIN_TABLE_NAME);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (!(int)$entity->getId()) {
            return $entity;
        }

        $items = $this->prepareProductListItems($entity->getId());
        $entity->setItems($items);

        return $entity;
    }

    /**
     * Retrieve product list objects
     *
     * @param int $listId
     * @return ProductListItemInterface[]
     * @throws \Exception
     */
    private function prepareProductListItems($listId)
    {
        $objects = [];
        $items = $this->loadProductListItems($listId);
        foreach ($items as $item) {
            $item[ProductListItemInterface::PRODUCT_OPTION] =
                $this->optionSerializer->unserializeToDataArray($item[ProductListItemInterface::PRODUCT_OPTION]);
            /** @var ProductListItemInterface $productListItem */
            $productListItem = $this->productListItemFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $productListItem,
                $item,
                ProductListItemInterface::class
            );
            $objects[] = $productListItem;
        }

        return $objects;
    }

    /**
     * Retrieve product list items
     *
     * @param int $listId
     * @return array
     * @throws \Exception
     */
    private function loadProductListItems($listId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->productListItemTable)
            ->where(ProductListItemInterface::LIST_ID . ' = :' . ProductListItemInterface::LIST_ID);
        return $connection->fetchAssoc($select, [ProductListItemInterface::LIST_ID => $listId]);
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
