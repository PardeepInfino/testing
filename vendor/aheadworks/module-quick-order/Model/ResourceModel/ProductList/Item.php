<?php
namespace Aheadworks\QuickOrder\Model\ResourceModel\ProductList;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel as MagentoFrameworkAbstractModel;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\AbstractResourceModel;

/**
 * Class Item
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel\ProductList
 */
class Item extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_qo_product_list_item';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, ProductListItemInterface::ITEM_ID);
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

    /**
     * Retrieve item ID by ites key
     *
     * @param string $itemKey
     * @return int|false
     * @throws LocalizedException
     */
    public function getIdByKey($itemKey)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where(ProductListItemInterface::ITEM_KEY . ' = :' . ProductListItemInterface::ITEM_KEY);

        return $connection->fetchOne($select, [ProductListItemInterface::ITEM_KEY => $itemKey]);
    }
}
