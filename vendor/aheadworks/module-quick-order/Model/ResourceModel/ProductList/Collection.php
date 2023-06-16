<?php
namespace Aheadworks\QuickOrder\Model\ResourceModel\ProductList;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList as ProductListResource;
use Aheadworks\QuickOrder\Model\ProductList as ProductListModel;

/**
 * Class Collection
 *
 * @package Aheadworks\QuickOrder\Model\ResourceModel\ProductList
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ProductListModel::class, ProductListResource::class);
    }
}
