<?php
namespace Aheadworks\QuickOrder\Api;

/**
 * Interface GuestCartManagementInterface
 * @api
 */
interface GuestCartManagementInterface
{
    /**
     * Add product list to cart
     *
     * @param int $listId
     * @param string $cartId
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addListToCart($listId, $cartId);
}
