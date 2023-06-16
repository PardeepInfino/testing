<?php
namespace Aheadworks\QuickOrder\Block\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;
use Aheadworks\QuickOrder\Api\CustomerManagementInterface;
use Aheadworks\QuickOrder\Model\Url as UrlModel;

/**
 * Class MyAccountLink
 *
 * @package Aheadworks\QuickOrder\Block\Customer
 */
class MyAccountLink extends LinkCurrent
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param TemplateContext $context
     * @param DefaultPathInterface $defaultPath
     * @param HttpContext $httpContext
     * @param CustomerManagementInterface $customerManagement
     * @param UrlModel $urlModel
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        DefaultPathInterface $defaultPath,
        HttpContext $httpContext,
        CustomerManagementInterface $customerManagement,
        UrlModel $urlModel,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->httpContext = $httpContext;
        $this->customerManagement = $customerManagement;
        $this->urlModel = $urlModel;
    }

    /**
     * Get url to quick order page for href attribute
     *
     * @return string
     */
    public function getHref()
    {
        return $this->urlModel->getUrlToQuickOrderPage();
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function _toHtml()
    {
        $isActive = $this->customerManagement->isActiveForCustomerGroup(
            $this->httpContext->getValue(Context::CONTEXT_GROUP),
            $this->_storeManager->getWebsite()->getId()
        );
        if (!$isActive) {
            return '';
        }
        return parent::_toHtml();
    }
}
