<?php
namespace Aheadworks\RequisitionLists\Model;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList as RequisitionListResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class RequisitionList
 * @package Aheadworks\RequisitionLists\Model
 */
class RequisitionList extends AbstractModel implements RequisitionListInterface, IdentityInterface
{
    /**
     * Requisition list cache tag
     */
    const CACHE_TAG = 'aw_requisition_list';

    /**
     * Requisition list listing cache tag
     */
    const LISTING_CACHE_TAG = 'aw_requisition_list_listing';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RequisitionListResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getListId()
    {
        return $this->getData(self::LIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setListId($listId)
    {
        return $this->setData(self::LIST_ID, $listId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($id)
    {
        return $this->setData(self::CUSTOMER_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getShared()
    {
        return $this->getData(self::SHARED);
    }

    /**
     * @inheritDoc
     */
    public function setShared($shared)
    {
        return $this->setData(self::SHARED, $shared);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
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
        \Aheadworks\RequisitionLists\Api\Data\RequisitionListExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities()
    {
        return [self::LISTING_CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }
}
