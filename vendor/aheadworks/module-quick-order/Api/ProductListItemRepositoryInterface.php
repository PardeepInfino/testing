<?php
namespace Aheadworks\QuickOrder\Api;

/**
 * Interface ProductListItemRepositoryInterface
 * @api
 */
interface ProductListItemRepositoryInterface
{
    /**
     * Retrieve product list item by its ID
     *
     * @param int $itemId
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($itemId);

    /**
     * Retrieve product list item by its key
     *
     * @param string $itemKey
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByKey($itemKey);

    /**
     * Save product list item
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListItemInterface $listItem
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListItemInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\QuickOrder\Api\Data\ProductListItemInterface $listItem);

    /**
     * Remove product list item by its ID
     *
     * @param int $itemId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($itemId);
}
