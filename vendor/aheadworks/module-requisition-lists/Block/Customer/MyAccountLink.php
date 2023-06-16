<?php
namespace Aheadworks\RequisitionLists\Block\Customer;

use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;
use Aheadworks\RequisitionLists\Model\Url as UrlModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;
use Magento\Customer\Block\Account\SortLinkInterface;

/**
 * Class MyAccountLink
 * @package Aheadworks\RequisitionLists\Block\Customer
 */
class MyAccountLink extends LinkCurrent implements SortLinkInterface
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
     * Get url to requisition list page for href attribute
     *
     * @return string
     */
    public function getHref()
    {
        return $this->urlModel->getUrlToRequisitionListPage();
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function _toHtml()
    {
        $isActive = $this->customerManagement->isActiveForCurrentWebsite(
            $this->_storeManager->getWebsite()->getId()
        );
        if (!$isActive) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData('sortOrder');
    }
}
