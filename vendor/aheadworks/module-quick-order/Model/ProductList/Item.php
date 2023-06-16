<?php
namespace Aheadworks\QuickOrder\Model\ProductList;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList\Item as ItemResource;
use Aheadworks\QuickOrder\Model\ProductList\Item\ObjectDataProcessor;

/**
 * Class Item
 *
 * @package Aheadworks\QuickOrder\Model\ProductList
 */
class Item extends AbstractModel implements ProductListItemInterface
{
    /**
     * @var ObjectDataProcessor
     */
    private $objectDataProcessor;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ObjectDataProcessor $objectDataProcessor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ObjectDataProcessor $objectDataProcessor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->objectDataProcessor = $objectDataProcessor;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ItemResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getItemId()
    {
        return $this->getData(ProductListItemInterface::ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setItemId($itemId)
    {
        return $this->setData(ProductListItemInterface::ITEM_ID, $itemId);
    }

    /**
     * @inheritdoc
     */
    public function getItemKey()
    {
        return $this->getData(ProductListItemInterface::ITEM_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setItemKey($itemKey)
    {
        return $this->setData(ProductListItemInterface::ITEM_KEY, $itemKey);
    }

    /**
     * @inheritdoc
     */
    public function getListId()
    {
        return $this->getData(ProductListItemInterface::LIST_ID);
    }

    /**
     * @inheritdoc
     */
    public function setListId($listId)
    {
        return $this->setData(ProductListItemInterface::LIST_ID, $listId);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(ProductListItemInterface::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($productId)
    {
        return $this->setData(ProductListItemInterface::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function getProductName()
    {
        return $this->getData(ProductListItemInterface::PRODUCT_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setProductName($productName)
    {
        return $this->setData(ProductListItemInterface::PRODUCT_NAME, $productName);
    }

    /**
     * @inheritdoc
     */
    public function getProductType()
    {
        return $this->getData(ProductListItemInterface::PRODUCT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setProductType($productType)
    {
        return $this->setData(ProductListItemInterface::PRODUCT_TYPE, $productType);
    }

    /**
     * @inheritdoc
     */
    public function getProductSku()
    {
        return $this->getData(ProductListItemInterface::PRODUCT_SKU);
    }

    /**
     * @inheritdoc
     */
    public function setProductSku($sku)
    {
        return $this->setData(ProductListItemInterface::PRODUCT_SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getProductQty()
    {
        return $this->getData(ProductListItemInterface::PRODUCT_QTY);
    }

    /**
     * @inheritdoc
     */
    public function setProductQty($qty)
    {
        return $this->setData(ProductListItemInterface::PRODUCT_QTY, $qty);
    }

    /**
     * @inheritdoc
     */
    public function getProductOption()
    {
        return $this->getData(ProductListItemInterface::PRODUCT_OPTION);
    }

    /**
     * @inheritdoc
     */
    public function setProductOption($productOption)
    {
        return $this->setData(ProductListItemInterface::PRODUCT_OPTION, $productOption);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $this->objectDataProcessor->prepareDataBeforeSave($this);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterLoad()
    {
        $this->objectDataProcessor->prepareDataAfterLoad($this);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\ProductListItemExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
