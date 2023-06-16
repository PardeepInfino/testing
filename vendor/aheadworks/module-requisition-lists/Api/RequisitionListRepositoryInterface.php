<?php
namespace Aheadworks\RequisitionLists\Api;

/**
 * Interface RequisitionListRepositoryInterface
 * @api
 */
interface RequisitionListRepositoryInterface
{
    /**
     * Retrieve list by its ID
     *
     * @param int $listId
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface
     * @thrown \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($listId);

    /**
     * Save list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list);

    /**
     * Retrieve list items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\RequisitionLists\Api\Data\RequisitionListSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete list
     *
     * @param \Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface $list);

    /**
     * Delete list by ID
     *
     * @param int $listId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($listId);
}
