<?php
namespace Aheadworks\RequisitionLists\Api;

/**
 * Interface RequisitionListManagementInterface
 * @api
 */
interface RequisitionListManagementInterface
{
    /**
     * Add item to list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $item
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function addItem($item);

    /**
     * Move item to list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $item
     * @param int $listIdToMove
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function moveItem($item, $listIdToMove);
}
