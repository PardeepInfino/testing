<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Model\ResourceModel;

use Amasty\VisualMerch\Model\Product\AdminhtmlDataProvider;

class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_category_product_static', null);
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return array
     */
    public function getProductPositionData(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['main_table' => $this->getMainTable()],
            ['product_id', 'position']
        )->where(
            'category_id = ?',
            $category->getId()
        )->where('product_id IN (?)', array_keys($category->getProductsPosition()));

        return $connection->fetchPairs($select);
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return $this
     */
    public function loadProductPositionData(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        $positionData = $this->getProductPositionData($category);
        $category->setProductPositionData($positionData);

        return $this;
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return $this
     */
    public function saveProductPositionData(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        $allPositionData = $category->getProductPositionData();

        if ($allPositionData === null) {
            return $this;
        }

        $connection = $this->getConnection();
        $connection->delete(
            $this->getMainTable(),
            [
                'category_id = ?' => $category->getId(),
                'product_id IN (?)' => array_keys($category->getProductsPosition())
            ]
        );

        $insertData = [];

        foreach ($allPositionData as $productId => $position) {
            $insertData[] = [
                'category_id' => $category->getId(),
                'product_id'  => $productId,
                'position'    => $position
            ];
        }

        if (!empty($insertData)) {
            $connection->insertOnDuplicate($this->getMainTable(), $insertData);
        }

        return $this;
    }
}
