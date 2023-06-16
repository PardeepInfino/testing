<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;

/**
 * Class RequisitionList
 * @package Aheadworks\RequisitionLists\Model\ResourceModel
 */
class RequisitionList extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_requisition_list';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RequisitionListInterface::LIST_ID);
    }
}
