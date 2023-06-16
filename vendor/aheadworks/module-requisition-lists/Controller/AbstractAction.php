<?php
namespace Aheadworks\RequisitionLists\Controller;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class AbstractAction
 * @package Aheadworks\RequisitionLists\Controller
 */
abstract class AbstractAction extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->responseFactory = $responseFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            $this->responseFactory->create()->setRedirect('/customer/account/login')->sendResponse();
        } elseif (!$this->isEntityBelongsToCustomer()) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * Check if entity belongs to current customer
     *
     * @return bool
     * @throws NotFoundException
     */
    abstract protected function isEntityBelongsToCustomer();

    /**
     * Retrieve current requisition list from request
     *
     * @return int|null
     */
    protected function getCurrentRequisitionListId()
    {
        return $this->getRequest()->getParam(RequisitionListInterface::LIST_ID, null);
    }
}
