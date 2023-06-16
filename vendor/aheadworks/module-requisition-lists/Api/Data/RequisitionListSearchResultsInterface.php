<?php
namespace Aheadworks\RequisitionLists\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class RequisitionListSearchResultsInterface
 * @api
 */
interface RequisitionListSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get product list items
     *
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface[]
     */
    public function getItems();

    /**
     * Set product list items
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
