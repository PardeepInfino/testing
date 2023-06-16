<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Provider
 */
class Provider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var RequisitionListRepositoryInterface
     */
    private $requisitionListRepository;

    /**
     * @var UrlInterface
     */
    private $_url;

    /**
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param RequestInterface $request
     * @param UrlInterface $_url
     */
    public function __construct(
        RequisitionListRepositoryInterface $requisitionListRepository,
        RequestInterface $request,
        UrlInterface $_url
    ) {
        $this->request = $request;
        $this->requisitionListRepository = $requisitionListRepository;
        $this->_url = $_url;
    }

    /**
     * Get requisition list name
     *
     * @param int $listId
     * @return string
     */
    public function getRequisitionListName($listId)
    {
        try {
            return $this->requisitionListRepository->get($listId)->getName();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Retrieve requisition list id from request
     *
     * @return string|null
     */
    public function getRequisitionListId()
    {
        return $this->request->getParam(RequisitionListInterface::LIST_ID, null);
    }

    /**
     * Get Requisition List Url
     *
     * @return string|null
     */
    public function getRequisitionListUrl($listId)
    {
        return $this->_url->getUrl(
            'aw_rl/rlist/edit/',
            [
                '_secure' => true,
                RequisitionListItemInterface::LIST_ID => $listId
            ]
        );
    }
}
