<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel\Sales\Order\Item;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;

/**
 * Class Collection
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\Sales\Order\Item
 */
class Collection extends OrderItemCollection
{
    /**
     * {@inheritDoc}
     */
    protected $_idFieldName = 'item_id';
}
