<?php
namespace Aheadworks\QuickOrder\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductListInterface
 * @api
 */
interface ProductListInterface extends ExtensibleDataInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const LIST_ID = 'list_id';
    const CUSTOMER_ID = 'customer_id';
    const UPDATED_AT = 'updated_at';
    const ITEMS = 'items';
    /**#@-*/

    /**
     * Get list ID
     *
     * @return int
     */
    public function getListId();

    /**
     * Set list ID
     *
     * @param int $listId
     * @return $this
     */
    public function setListId($listId);

    /**
     * Get customer ID
     *
     * @return string
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get items
     *
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListItemInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListItemInterface[] $items
     * @return $this
     */
    public function setItems($items);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\ProductListExtensionInterface $extensionAttributes
    );
}
