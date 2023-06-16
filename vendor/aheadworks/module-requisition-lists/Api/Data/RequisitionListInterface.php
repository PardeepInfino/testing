<?php
namespace Aheadworks\RequisitionLists\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RequisitionListInterface
 * @api
 */
interface RequisitionListInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const LIST_ID = 'list_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const CUSTOMER_ID = 'customer_id';
    const SHARED = 'shared';
    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Get List ID
     *
     * @return int
     */
    public function getListId();

    /**
     * Set List ID
     *
     * @param int|null $listId
     * @return $this
     */
    public function setListId($listId);

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get Customer ID
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set Customer ID
     *
     * @param int $id
     * @return $this
     */
    public function setCustomerId($id);

    /**
     * Get Shared
     *
     * @return bool
     */
    public function getShared();

    /**
     * Set Shared
     *
     * @param bool $shared
     * @return $this
     */
    public function setShared($shared);

    /**
     * Get Updated At
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\RequisitionLists\Api\Data\RequisitionListExtensionInterface $extensionAttributes
    );
}
