<?php
namespace Aheadworks\RequisitionLists\Model;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Url
 * @package Aheadworks\RequisitionLists\Model
 */
class Url
{
    /**
     * Route for Requisition List Page
     */
    const REQUISITION_LIST_ROUTE = 'aw_rl/rlist';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve url to requisition list page
     *
     * @return string
     */
    public function getUrlToRequisitionListPage()
    {
        return $this->urlBuilder->getUrl(self::REQUISITION_LIST_ROUTE);
    }

    /**
     * Retrieve edit list url
     *
     * @param int $listId
     * @return string
     */
    public function getEditListUrl($listId)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/edit',
            [
                RequisitionListInterface::LIST_ID => $listId
            ]
        );
    }

    /**
     * Retrieve delete list url
     *
     * @param int $listId
     * @return string
     */
    public function getDeleteListUrl($listId)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/delete',
            [
                RequisitionListInterface::LIST_ID => $listId
            ]
        );
    }

    /**
     * Retrieve update list url
     *
     * @param int $listId
     * @return string
     */
    public function getUpdateListUrl($listId)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/save',
            [
                RequisitionListInterface::LIST_ID => $listId
            ]
        );
    }

    /**
     * Retrieve add to list url
     *
     * @param int|null $listId
     * @return string
     */
    public function getAddToListUrl($listId = null)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/add',
            $listId ? [RequisitionListInterface::LIST_ID => $listId] : []
        );
    }

    /**
     * Retrieve add to list order url
     *
     * @param int|null $listId
     * @return string
     */
    public function getAddToListOrderUrl($listId = null)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/orderAdd',
            $listId ? [RequisitionListInterface::LIST_ID => $listId] : []
        );
    }

    /**
     * Retrieve add to list from cart url
     *
     * @param int|null $listId
     * @return string
     */
    public function getAddToListFromCartUrl($listId = null)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/addfromcart',
            $listId ? [RequisitionListInterface::LIST_ID => $listId] : []
        );
    }

    /**
     * Retrieve configure item url
     *
     * @param int|null $listId
     * @return string
     */
    public function getConfigureItemUrl($listId = null)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/configure'
        );
    }

    /**
     * Retrieve update item url
     *
     * @param int|null $listId
     * @return string
     */
    public function getUpdateItemOptionUrl($listId = null)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/updateOption'
        );
    }

    /**
     * Retrieve add to list url
     *
     * @param int|null $listId
     * @return string
     */
    public function getRemoveItemUrl($listId = null)
    {
        return $this->urlBuilder->getUrl(
            self::REQUISITION_LIST_ROUTE . '/deleteItem',
            $listId ? [RequisitionListInterface::LIST_ID => $listId] : []
        );
    }
}
