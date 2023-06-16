<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Controller\AbstractAction;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class AbstractRequisitionListAction
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
abstract class AbstractRequisitionListAction extends AbstractAction
{
    /**
     * @var RequisitionListRepositoryInterface
     */
    protected $requisitionListRepository;

    /**
     * @var string
     */
    protected $currentRequisitionListName = '';

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        PageFactory $pageFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $responseFactory,
            $pageFactory
        );
        $this->requisitionListRepository = $requisitionListRepository;
        $this->provider = $provider;
    }

    /**
     * Retrieve Requisition List
     *
     * @return RequisitionListInterface
     * @throws NotFoundException
     */
    protected function getEntity()
    {
        try {
            $entity = $this->requisitionListRepository->get($this->getCurrentRequisitionListId());
            $this->currentRequisitionListName = $entity->getName();
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    protected function isEntityBelongsToCustomer()
    {
        $entity = $this->getEntity();

        if (!$entity->getListId()
            || $this->customerSession->getCustomerId() != $entity->getCustomerId()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get current requisition list name
     */
    protected function getCurrentRequisitionListName()
    {
        return $this->currentRequisitionListName;
    }

    /**
     * Resolve response
     *
     * @param string $backUrl
     * @return ResponseInterface|ResultInterface
     */
    protected function goBack($backUrl = null)
    {
        $result = [];

        if ($backUrl) {
            $result['backUrl'] = $backUrl;
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );

        return $this->getResponse();
    }

    /**
     * Resolve list data
     *
     * @param array|ProductInterface $items
     * @return array
     */
    public function resolveListData($items)
    {
        $items = !is_array($items) ? [$items] : $items;

        return [
            'items' => $items,
            'requisition_list_name' => $this->getCurrentRequisitionListName(),
            'requisition_list_url' => $this->provider->getRequisitionListUrl($this->getCurrentRequisitionListId())
        ];
    }
}
