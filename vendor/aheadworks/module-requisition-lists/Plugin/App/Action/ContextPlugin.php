<?php
namespace Aheadworks\RequisitionLists\Plugin\App\Action;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Class ContextPlugin
 * @package Aheadworks\RequisitionLists\Plugin\App\Action
 */
class ContextPlugin
{
    /**
     * @var Context
     */
    private $httpContext;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @param Session $customerSession
     * @param Context $httpContext
     */
    public function __construct(
        Session $customerSession,
        Context $httpContext
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * Set current customer id value to Http Context
     *
     * @param ActionInterface $subject
     * @param RequestInterface $request
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            $customerId = 0;
        }

        $this->httpContext->setValue(
            RequisitionListInterface::CUSTOMER_ID,
            $customerId,
            false
        );
    }
}
