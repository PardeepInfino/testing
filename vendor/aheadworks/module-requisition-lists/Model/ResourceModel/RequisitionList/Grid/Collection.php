<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Grid;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Collection as ListCollection;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList as RequisitionListResource;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item as RequisitionListItemResource;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;

/**
 * Class Collection
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Grid
 */
class Collection extends ListCollection implements SearchResultInterface
{
    /**
     * Items count column name
     */
    const REQUISITION_LIST_ITEMS_COUNT_COLUMN_NAME = 'list_items_count';

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(Document::class, RequisitionListResource::class);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function _beforeLoad()
    {
        $itemsCountExpr = new \Zend_Db_Expr('COUNT(list_items.' . RequisitionListItemInterface::LIST_ID . ')');

        $this->getSelect()
            ->joinLeft(
                ['list_items' => $this->getTable(RequisitionListItemResource::MAIN_TABLE_NAME)],
                'list_items.list_id = main_table.list_id',
                [
                    self::REQUISITION_LIST_ITEMS_COUNT_COLUMN_NAME => $itemsCountExpr
                ]
            )
            ->group('main_table.' . RequisitionListInterface::LIST_ID);

        return parent::_beforeLoad();
    }
}
