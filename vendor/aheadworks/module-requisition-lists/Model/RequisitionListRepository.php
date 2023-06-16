<?php
namespace Aheadworks\RequisitionLists\Model;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterfaceFactory;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListSearchResultsInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListSearchResultsInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList as RequisitionListResource;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\CollectionFactory;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Collection as RequisitionListCollection;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RequisitionListRepository
 * @package Aheadworks\RequisitionLists\Model
 */
class RequisitionListRepository implements RequisitionListRepositoryInterface
{
    /**
     * @var RequisitionListResource
     */
    private $resource;

    /**
     * @var RequisitionListInterfaceFactory
     */
    private $requisitionListFactory;

    /**
     * @var CollectionFactory
     */
    private $requisitionListCollectionFactory;

    /**
     * @var RequisitionListSearchResultsInterfaceFactory
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
     * @param RequisitionListResource $resource
     * @param RequisitionListInterfaceFactory $requisitionListFactory
     * @param CollectionFactory $requisitionListCollectionFactory
     * @param RequisitionListSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        RequisitionListResource $resource,
        RequisitionListInterfaceFactory $requisitionListFactory,
        CollectionFactory $requisitionListCollectionFactory,
        RequisitionListSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->requisitionListFactory = $requisitionListFactory;
        $this->requisitionListCollectionFactory = $requisitionListCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritDoc
     */
    public function get($listId)
    {
        if (!isset($this->registry[$listId])) {
            /** @var RequisitionListInterface $list */
            $list = $this->requisitionListFactory->create();
            $this->resource->load($list, $listId);
            if (!$list->getListId()) {
                throw NoSuchEntityException::singleField(RequisitionListInterface::LIST_ID, $listId);
            }
            $this->registry[$listId] = $list;
        }

        return $this->registry[$listId];
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RequisitionListCollection $collection */
        $collection = $this->requisitionListCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RequisitionListInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RequisitionListSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var RequisitionList $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(RequisitionListInterface $list)
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
     * {@inheritdoc}
     */
    public function delete(RequisitionListInterface $list)
    {
        try {
            $this->resource->delete($list);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$list->getId()])) {
            unset($this->registry[$list->getId()]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($listId)
    {
        return $this->delete($this->get($listId));
    }

    /**
     * Retrieves data object using model
     *
     * @param RequisitionList $model
     * @return RequisitionListInterface
     */
    private function getDataObject($model)
    {
        /** @var RequisitionListInterface $object */
        $object = $this->requisitionListFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, RequisitionListInterface::class),
            RequisitionListInterface::class
        );

        return $object;
    }
}
