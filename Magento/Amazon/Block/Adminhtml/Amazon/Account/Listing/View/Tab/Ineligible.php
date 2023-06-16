<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\View\Tab;

use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Text\ListText;

/**
 * Class Ineligible
 */
class Ineligible extends ListText implements TabInterface
{
    /** @var ListingManagementInterface $listingManagement */
    private $listingManagement;
    /** @var Http $request */
    private $request;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ListingManagementInterface $listingManagement
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        ListingManagementInterface $listingManagement,
        Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->listingManagement = $listingManagement;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Ineligible - ' . $this->fetchRecordCount());
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Ineligible Listings');
    }

    /**
     * Get row count by condition
     *
     * @return int
     */
    private function fetchRecordCount()
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');
        /** @var array */
        $conditions = [
            Definitions::NO_LONGER_ELIGIBLE_STATUS
        ];

        /** @var int */
        return $this->listingManagement->getCountByListStatus($conditions, $merchantId);
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}