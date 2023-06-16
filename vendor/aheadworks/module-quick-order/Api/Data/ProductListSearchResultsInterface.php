<?php
namespace Aheadworks\QuickOrder\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ProductListSearchResultsInterface
 * @api
 */
interface ProductListSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get product list items
     *
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListInterface[]
     */
    public function getItems();

    /**
     * Set product list items
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
