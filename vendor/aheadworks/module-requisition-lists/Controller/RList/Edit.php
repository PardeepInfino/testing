<?php
namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Edit
 */
class Edit extends AbstractRequisitionListAction
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
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerManagementInterface $customerManagement
     */
    public function __construct(
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        CustomerManagementInterface $customerManagement
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
        $page->getConfig()->getTitle()->set($this->getCurrentRequisitionListName());

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
}
