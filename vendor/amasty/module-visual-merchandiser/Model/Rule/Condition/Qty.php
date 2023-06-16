<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Model\Rule\Condition;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;

class Qty extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    private $stockStatus;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatus,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->stockStatus = $stockStatus;
        $this->moduleManager = $moduleManager;
    }

    public function getAttributeElementHtml()
    {
        return __('Qty');
    }

    protected function _getAttributeCode()
    {
        return 'qty';
    }

    /**
     * @param Select $select
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function getQtyColumn($select)
    {
        $fromTables = $select->getPart(Select::FROM);
        $catalogInventoryTable = $this->stockStatus->getMainTable();
        if ($this->isMsiEnabled() && $fromTables['stock_status']['tableName'] != $catalogInventoryTable) {
            $qtyColumn = 'quantity';
        } else {
            $qtyColumn = 'stock_status.qty';
            $fromTables['stock_status']['joinCondition'] = preg_replace(
                '@(stock_status.website_id=)\d+@',
                '$1 0',
                $fromTables['stock_status']['joinCondition']
            );
            $select->setPart(Select::FROM, $fromTables);
        }

        return $qtyColumn;
    }

    /**
     * @param Select $select
     * @return bool
     * @throws \Zend_Db_Select_Exception
     */
    protected function isStockStatusJoined($select)
    {
        $fromTables = $select->getPart(Select::FROM);

        return isset($fromTables['stock_status']);
    }

    /**
     * @param Collection $productCollection
     * @return AbstractCondition|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function collectValidatedAttributes($productCollection)
    {
        $select = $productCollection->getSelect();
        $value     = $this->getValue();
        $operator  = $this->getOperatorForValidate();

        if (!$this->isStockStatusJoined($select)) {
            $this->stockStatus->addStockStatusToSelect($select, $this->getStoreManager()->getWebsite());
        }

        $this->_condition = $this->getOperatorCondition($this->getQtyColumn($select), $operator, $value);
    }

    /**
     * @param $field
     * @param $operator
     * @param $value
     * @return string
     */
    public function getOperatorCondition($field, $operator, $value)
    {
        return sprintf(
            '(%s AND e.type_id = "%s")',
            parent::getOperatorCondition($field, $operator, $value),
            Type::TYPE_SIMPLE
        );
    }

    /**
     * @return bool
     */
    private function isMsiEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }
}
