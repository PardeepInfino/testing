<?php
namespace Aheadworks\RequisitionLists\Model;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterfaceFactory;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemSearchResultsInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemSearchResultsInterfaceFactory;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item as RequisitionListItem;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item as RequisitionListItemResource;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\CollectionFactory;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item\Collection as RequisitionListItemCollection;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class RequisitionListItemRepository
 * @package Aheadworks\RequisitionLists\Model
 */
class RequisitionListItemRepository implements RequisitionListItemRepositoryInterface
{
    /**
     * @var RequisitionListItemResource
     */
    private $resource;

    /**
     * @var RequisitionListItemInterfaceFactory
     */
    private $requisitionListItemFactory;

    /**
     * @var CollectionFactory
     */
    private $requisitionListItemCollectionFactory;

    /**
     * @var RequisitionListItemSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

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
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
    * @var array
    */
    private $registry = [];

    /**
     * @param RequisitionListItemResource $resource
     * @param RequisitionListItemInterfaceFactory $requisitionListItemFactory
     * @param CollectionFactory $requisitionListItemCollectionFactory
     * @param RequisitionListItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        RequisitionListItemResource $resource,
        RequisitionListItemInterfaceFactory $requisitionListItemFactory,
        CollectionFactory $requisitionListItemCollectionFactory,
        RequisitionListItemSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->requisitionListItemFactory = $requisitionListItemFactory;
        $this->requisitionListItemCollectionFactory = $requisitionListItemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @inheritDoc
     */
    public function get($listItemId)
    {
        if (!isset($this->registry[$listItemId])) {
            /** @var RequisitionListItemInterface $listItem */
            $listItem = $this->requisitionListItemFactory->create();
            $this->resource->load($listItem, $listItemId);
            if (!$listItem->getItemId()) {
                throw NoSuchEntityException::singleField(RequisitionListItemInterface::ITEM_ID, $listItemId);
            }
            $this->registry[$listItemId] = $listItem;
        }

        return $this->registry[$listItemId];
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RequisitionListItemCollection $collection */
        $collection = $this->requisitionListItemCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RequisitionListItemInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var RequisitionListItemSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var RequisitionListItem $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(RequisitionListItemInterface $listItem)
    {
        try {
            $this->resource->save($listItem);
            $this->registry[$listItem->getItemId()] = $listItem;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $listItem;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RequisitionListItemInterface $listItem)
    {
        try {
            $this->resource->delete($listItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$listItem->getId()])) {
            unset($this->registry[$listItem->getId()]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($listItemId)
    {
        return $this->delete($this->get($listItemId));
    }


    /**
     * Retrieves data object using model
     *
     * @param RequisitionListItem $model
     * @return RequisitionListItemInterface
     */
    private function getDataObject($model)
    {
        /** @var RequisitionListItemInterface $object */
        $object = $this->requisitionListItemFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, RequisitionListItemInterface::class),
            RequisitionListItemInterface::class
        );

        return $object;
    }
}
