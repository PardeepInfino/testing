<?php
namespace Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\AbstractResourceModel;
use Magento\Framework\Model\AbstractModel as MagentoFrameworkAbstractModel;

/**
 * Class Item
 * @package Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList
 */
class Item extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_requisition_list_item';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RequisitionListItemInterface::ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function save(MagentoFrameworkAbstractModel $object)
    {
        $object->beforeSave();
        return parent::save($object);
    }

    /**
     * @inheritdoc
     */
    public function load(MagentoFrameworkAbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $this->entityManager->load($object, $objectId, []);
            $object->afterLoad();
        }

        return $this;
    }
}
