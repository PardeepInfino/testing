<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model;

use Amasty\Feed\Api\Data\FeedInterface;
use Amasty\Feed\Api\FeedRepositoryInterface;
use Amasty\Feed\Model\Filesystem\FeedOutput;
use Amasty\Feed\Model\Indexer\Feed\FeedRuleProcessor;
use Amasty\Feed\Model\ResourceModel\Feed as FeedResource;
use Amasty\Feed\Model\ResourceModel\Feed\Collection;
use Amasty\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class FeedRepository implements FeedRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var FeedFactory
     */
    private $feedFactory;

    /**
     * @var FeedResource
     */
    private $feedResource;

    /**
     * Model data storage
     *
     * @var FeedInterface[]
     */
    private $feeds;

    /**
     * @var CollectionFactory
     */
    private $feedCollectionFactory;

    /**
     * @var FeedRuleProcessor
     */
    private $feedRuleProcessor;

    /**
     * @var FeedOutput
     */
    private $feedOutput;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        FeedFactory $feedFactory,
        FeedResource $feedResource,
        CollectionFactory $feedCollectionFactory,
        FeedRuleProcessor $feedRuleProcessor,
        FeedOutput $feedOutput
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->feedFactory = $feedFactory;
        $this->feedResource = $feedResource;
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->feedRuleProcessor = $feedRuleProcessor;
        $this->feedOutput = $feedOutput;
    }

    /**
     * @inheritdoc
     */
    public function save(FeedInterface $feed, $withReindex = false)
    {
        try {
            if ($feed->getEntityId()) {
                $feed = $this->getById($feed->getEntityId())->addData($feed->getData());
            }
            $this->feedResource->save($feed);

            if ($withReindex && !$this->feedRuleProcessor->getIndexer(FeedRuleProcessor::INDEXER_ID)->isScheduled()) {
                $this->feedRuleProcessor->reindexRow($feed->getId());
            }

            unset($this->feeds[$feed->getEntityId()]);
        } catch (\Exception $e) {
            if ($feed->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save feed with ID %1. Error: %2',
                        [$feed->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new feed. Error: %1', $e->getMessage()));
        }

        return $feed;
    }

    /**
     * @inheritdoc
     */
    public function getById($feedId)
    {
        if (!isset($this->feeds[$feedId])) {
            /** @var \Amasty\Feed\Model\Feed $feed */
            $feed = $this->feedFactory->create();
            $this->feedResource->load($feed, $feedId);
            if (!$feed->getId()) {
                throw new NoSuchEntityException(__('Feed with specified ID "%1" not found.', $feedId));
            }
            $this->feeds[$feedId] = $feed;
        }

        return $this->feeds[$feedId];
    }

    /**
     * @inheritdoc
     */
    public function getEmptyModel()
    {
        return $this->feedFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function delete(FeedInterface $feed)
    {
        try {
            $this->feedResource->delete($feed);
            $this->feedOutput->delete($feed);
            unset($this->feeds[$feed->getEntityId()]);
        } catch (\Exception $e) {
            if ($feed->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove feed with ID %1. Error: %2',
                        [$feed->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove feed. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($feedId)
    {
        try {
            $feedModel = $this->getById($feedId);
            $this->delete($feedModel);
        } catch (NoSuchEntityException $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove feed with ID %1. Error: %2',
                    [$feedId, $exception->getMessage()]
                )
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Feed\Model\ResourceModel\Feed\Collection $feedCollection */
        $feedCollection = $this->feedCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $feedCollection);
        }

        $searchResults->setTotalCount($feedCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $feedCollection);
        }

        $feedCollection->setCurPage($searchCriteria->getCurrentPage());
        $feedCollection->setPageSize($searchCriteria->getPageSize());

        $feeds = [];
        /** @var FeedInterface $feed */
        foreach ($feedCollection->getItems() as $feed) {
            $feeds[] = $this->getById($feed->getEntityId());
        }

        $searchResults->setItems($feeds);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $feedCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $feedCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $feedCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $feedCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $feedCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $feedCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
