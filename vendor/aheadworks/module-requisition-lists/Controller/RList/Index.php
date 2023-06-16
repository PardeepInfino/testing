<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

/**
 * Class Index
 * @package Aheadworks\RequisitionLists\Controller\RList
 */
class Index extends AbstractRequisitionListAction
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Provider $provider
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param CustomerManagementInterface $customerManagement
     * @param PageFactory $pageFactory
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     */
    public function __construct(
        Provider $provider,
        Context $context,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        CustomerManagementInterface $customerManagement,
        PageFactory $pageFactory,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository
    ) {
        parent::__construct(
            $provider,
            $context,
            $customerSession,
            $responseFactory,
            $requisitionListRepository,
            $pageFactory
        );
        $this->storeManager = $storeManager;
        $this->customerManagement = $customerManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Requisition Lists'));

        return $page;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $isActive = $this->customerManagement->isActiveForCurrentWebsite(
            $this->storeManager->getWebsite()->getId()
        );
        if (!$isActive) {
            $this->getResponse()->setRedirect($this->_url->getBaseUrl());
        }

        return parent::dispatch($request);
    }

    /**
     * @inheritdoc
     */
    protected function isEntityBelongsToCustomer()
    {
        return true;
    }
}
