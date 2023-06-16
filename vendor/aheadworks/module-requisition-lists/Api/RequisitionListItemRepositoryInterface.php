<?php
namespace Aheadworks\RequisitionLists\Api;

/**
 * Interface RequisitionListItemRepositoryInterface
 * @api
 */
interface RequisitionListItemRepositoryInterface
{
    /**
     * Retrieve list item by its ID
     *
     * @param int $listItemId
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($listItemId);

    /**
     * Retrieve list items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Save list item
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem);

    /**
     * Delete list item
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface $listItem);


    /**
     * Delete list item by ID
     *
     * @param int $listItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($listItemId);
}
