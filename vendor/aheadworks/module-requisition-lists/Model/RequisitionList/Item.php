<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\ObjectDataProcessor;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList\Item as RequisitionListItemResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Item
 * @package Aheadworks\RequisitionLists\Model
 */
class Item extends AbstractModel implements RequisitionListItemInterface
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
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(RequisitionListItemResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getItemId()
    {
        return $this->getData(RequisitionListItemInterface::ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setItemId($itemId)
    {
        return $this->setData(RequisitionListItemInterface::ITEM_ID, $itemId);
    }

    /**
     * @inheritdoc
     */
    public function getListId()
    {
        return $this->getData(RequisitionListItemInterface::LIST_ID);
    }

    /**
     * @inheritdoc
     */
    public function setListId($listId)
    {
        return $this->setData(RequisitionListItemInterface::LIST_ID, $listId);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(RequisitionListItemInterface::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($productId)
    {
        return $this->setData(RequisitionListItemInterface::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function getProductName()
    {
        return $this->getData(RequisitionListItemInterface::PRODUCT_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setProductName($productName)
    {
        return $this->setData(RequisitionListItemInterface::PRODUCT_NAME, $productName);
    }

    /**
     * @inheritdoc
     */
    public function getProductSku()
    {
        return $this->getData(RequisitionListItemInterface::PRODUCT_SKU);
    }

    /**
     * @inheritdoc
     */
    public function setProductSku($sku)
    {
        return $this->setData(RequisitionListItemInterface::PRODUCT_SKU, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getProductQty()
    {
        return $this->getData(RequisitionListItemInterface::PRODUCT_QTY);
    }

    /**
     * @inheritdoc
     */
    public function setProductQty($qty)
    {
        return $this->setData(RequisitionListItemInterface::PRODUCT_QTY, $qty);
    }

    /**
     * @inheritdoc
     */
    public function getProductOption()
    {
        return $this->getData(RequisitionListItemInterface::PRODUCT_OPTION);
    }

    /**
     * @inheritdoc
     */
    public function setProductOption($productOption)
    {
        return $this->setData(RequisitionListItemInterface::PRODUCT_OPTION, $productOption);
    }

    /**
     * @inheritdoc
     */
    public function getProductType()
    {
        return $this->getData(RequisitionListItemInterface::PRODUCT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setProductType($productType)
    {
        return $this->setData(RequisitionListItemInterface::PRODUCT_TYPE, $productType);
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
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
