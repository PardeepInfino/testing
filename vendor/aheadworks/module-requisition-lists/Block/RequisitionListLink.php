<?php
namespace Aheadworks\RequisitionLists\Block;

use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;
use Aheadworks\RequisitionLists\Model\Url;
use Magento\Customer\Block\Account\SortLinkInterface;
use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class RequisitionListLink
 * @package Aheadworks\RequisitionLists\Block
 */
class RequisitionListLink extends Link implements SortLinkInterface
{
    /**
     * @var Url
     */
    private $urlBuilder;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @param TemplateContext $context
     * @param Url $url
     * @param CustomerManagementInterface $customerManagement
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Url $url,
        CustomerManagementInterface $customerManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $url;
        $this->customerManagement = $customerManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function getHref()
    {
        return $this->urlBuilder->getUrlToRequisitionListPage();
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return __('My Requisition Lists');
    }

    /**
     * {@inheritDoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritDoc}
     */
    public function _toHtml()
    {
        $isActive = $this->customerManagement->isActiveForCurrentWebsite(
            $this->_storeManager->getWebsite()->getId()
        );
        if (!$isActive) {
            return '';
        }

        return parent::_toHtml();
    }
}
