<?php
namespace Aheadworks\QuickOrder\Model\ProductList;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterfaceFactory;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item as ItemResource;

/**
 * Class ItemRepository
 *
 * @package Aheadworks\QuickOrder\Model\ProductList
 */
class ItemRepository implements ProductListItemRepositoryInterface
{
    /**
     * @var ItemResource
     */
    private $resource;

    /**
     * @var ProductListItemInterfaceFactory
     */
    private $productListItemFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByKey = [];

    /**
     * @param ItemResource $resource
     * @param ProductListItemInterfaceFactory $productListItemFactory
     */
    public function __construct(
        ItemResource $resource,
        ProductListItemInterfaceFactory $productListItemFactory
    ) {
        $this->resource = $resource;
        $this->productListItemFactory = $productListItemFactory;
    }

    /**
     * @inheritdoc
     */
    public function get($itemId)
    {
        if (!isset($this->registry[$itemId])) {
            /** @var ProductListItemInterface $item */
            $item = $this->productListItemFactory->create();
            $this->resource->load($item, $itemId);
            if (!$item->getItemId()) {
                throw NoSuchEntityException::singleField(ProductListItemInterface::ITEM_ID, $itemId);
            }
            $this->registry[$itemId] = $item;
        }
        return $this->registry[$itemId];
    }

    /**
     * @inheritdoc
     */
    public function getByKey($itemKey)
    {
        if (!isset($this->registryByKey[$itemKey])) {
            $itemId = $this->resource->getIdByKey($itemKey);
            if (!$itemId) {
                throw NoSuchEntityException::singleField(ProductListItemInterface::ITEM_KEY, $itemKey);
            }
            /** @var ProductListItemInterface $item */
            $item = $this->productListItemFactory->create();
            $this->resource->load($item, $itemId);
            $this->registry[$itemId] = $item;
            $this->registryByKey[$itemKey] = $item;
        }
        return $this->registryByKey[$itemKey];
    }

    /**
     * @inheritdoc
     */
    public function save(ProductListItemInterface $item)
    {
        try {
            $this->resource->save($item);
            $this->registry[$item->getItemId()] = $item;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($itemId)
    {
        try {
            $item = $this->get($itemId);
            $this->resource->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$itemId])) {
            unset($this->registry[$itemId]);
        }

        return true;
    }
}
