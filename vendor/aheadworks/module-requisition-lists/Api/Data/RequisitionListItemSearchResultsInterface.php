<?php
namespace Aheadworks\RequisitionLists\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class RequisitionListItemSearchResultsInterface
 * @api
 */
interface RequisitionListItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list items
     *
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface[]
     */
    public function getItems();

    /**
     * Set list items
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
