<?php
namespace Aheadworks\QuickOrder\Model;

use Aheadworks\QuickOrder\Api\ProductListRepositoryInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListInterfaceFactory;
use Aheadworks\QuickOrder\Api\Data\ProductListSearchResultsInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListSearchResultsInterfaceFactory;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList as ProductListResource;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Collection as ProductListCollection;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\CollectionFactory as ProductListCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class ProductListRepository
 *
 * @package Aheadworks\QuickOrder\Model
 */
class ProductListRepository implements ProductListRepositoryInterface
{
    /**
     * @var ProductListResource
     */
    private $resource;

    /**
     * @var ProductListInterfaceFactory
     */
    private $productListFactory;

    /**
     * @var ProductListCollectionFactory
     */
    private $productListCollectionFactory;

    /**
     * @var ProductListSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByCustomerId = [];

    /**
     * @param ProductListResource $resource
     * @param ProductListInterfaceFactory $productListFactory
     * @param ProductListCollectionFactory $productListCollectionFactory
     * @param ProductListSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ProductListResource $resource,
        ProductListInterfaceFactory $productListFactory,
        ProductListCollectionFactory $productListCollectionFactory,
        ProductListSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->productListFactory = $productListFactory;
        $this->productListCollectionFactory = $productListCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritdoc
     */
    public function get($listId)
    {
        if (!isset($this->registry[$listId])) {
            /** @var ProductListInterface $list */
            $list = $this->productListFactory->create();
            $this->resource->load($list, $listId);
            if (!$list->getListId()) {
                throw NoSuchEntityException::singleField(ProductListInterface::LIST_ID, $listId);
            }
            $this->registry[$listId] = $list;
        }
        return $this->registry[$listId];
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerId($customerId)
    {
        if (!isset($this->registryByCustomerId[$customerId])) {
            $listId = $this->resource->getListIdByCustomerId($customerId);
            if (!$listId) {
                throw NoSuchEntityException::singleField(ProductListInterface::CUSTOMER_ID, $customerId);
            }
            /** @var ProductListInterface $list */
            $list = $this->productListFactory->create();
            $this->resource->load($list, $listId);
            $this->registry[$listId] = $list;
            $this->registryByCustomerId[$customerId] = $list;
        }
        return $this->registryByCustomerId[$customerId];
    }

    /**
     * @inheritdoc
     */
    public function save(ProductListInterface $list)
    {
        try {
            $this->resource->save($list);
            $this->registry[$list->getListId()] = $list;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ProductListCollection $collection */
        $collection = $this->productListCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, ProductListInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProductListSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var ProductList $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param ProductList $model
     * @return ProductListInterface
     */
    private function getDataObject($model)
    {
        /** @var ProductListInterface $object */
        $object = $this->productListFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, ProductListInterface::class),
            ProductListInterface::class
        );
        return $object;
    }
}
