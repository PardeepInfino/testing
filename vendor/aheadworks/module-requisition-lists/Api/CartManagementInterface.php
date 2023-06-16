<?php
namespace Aheadworks\RequisitionLists\Api;

/**
 * Interface CartManagementInterface
 * @api
 */
interface CartManagementInterface
{
    /**
     * Add list of products to cart
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface[] $items
     * @param int $cartId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addItemsToCart($items, $cartId);
}
