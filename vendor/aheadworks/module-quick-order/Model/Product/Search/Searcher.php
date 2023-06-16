<?php
namespace Aheadworks\QuickOrder\Model\Product\Search;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\DataObject;
use Magento\Search\Api\SearchInterface;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplierFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class Searcher
 *
 * @package Aheadworks\QuickOrder\Model\Product\Search
 */
class Searcher
{
    /**
     * Page size
     */
    const RESULT_SIZE = 6;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchInterface
     */
    private $search;

    /**
     * @var SearchResultApplierFactory
     */
    private $searchResultApplierFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchInterface $search
     * @param FilterBuilder $filterBuilder
     * @param SearchResultApplierFactory $searchResultApplierFactory
     * @param ProductCollectionFactory $collectionFactory
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchInterface $search,
        FilterBuilder $filterBuilder,
        SearchResultApplierFactory $searchResultApplierFactory,
        ProductCollectionFactory $collectionFactory,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->search = $search;
        $this->filterBuilder = $filterBuilder;
        $this->searchResultApplierFactory = $searchResultApplierFactory;
        $this->collectionFactory = $collectionFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Make search using provided search item
     *
     * @param string $searchTerm
     * @return DataObject[]|ProductInterface[]
     */
    public function search($searchTerm)
    {
        $searchResult = $this->runSearchEngine($searchTerm);
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $orders = [
            'relevance'=> 'DESC',
            'entity_id' => 'DESC'
        ];
        $applier = $this->searchResultApplierFactory->create(
            [
                'collection' => $collection,
                'searchResult' => $searchResult,
                'orders' => $orders,
            ]
        );
        $applier->apply();

        return $collection->getItems();
    }

    /**
     * Run search engine and return relevant results
     *
     * @param string $searchTerm
     * @return SearchResultInterface
     */
    private function runSearchEngine($searchTerm)
    {
        $searchTermFilter = $this->filterBuilder->setField('search_term')
            ->setValue($searchTerm)
            ->setConditionType('eq')
            ->create();
        $visibilityFilter = $this->filterBuilder->setField('visibility')
            ->setValue([Visibility::VISIBILITY_IN_SEARCH, Visibility::VISIBILITY_BOTH])
            ->setConditionType('in')
            ->create();
        $sortOrder = $this->sortOrderBuilder
            ->setField('entity_id')
            ->setDirection('ASC')
            ->create();

        $this->searchCriteriaBuilder->addFilter($searchTermFilter);
        $this->searchCriteriaBuilder->addFilter($visibilityFilter);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria
            ->setRequestName('quick_search_container')
            ->setPageSize(self::RESULT_SIZE)
            ->setSortOrders([$sortOrder]);

        return $this->search->search($searchCriteria);
    }
}
