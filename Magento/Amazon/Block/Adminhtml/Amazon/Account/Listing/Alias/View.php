<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Alias;

use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class View
 */
class View extends Container
{
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;

    /**
     * @param Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ListingRepositoryInterface $listingRepository,
        array $data = []
    ) {
        $this->listingRepository = $listingRepository;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_account_listing_alias';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_account_listing_alias');

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');

        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {

            /** @var ListingRepositoryInterface */
            $listing = $this->listingRepository->getById($id);
            /** @var int */
            $merchantId = $listing->getMerchantId();
            /** @var string */
            $previousTab = $this->getRequest()->getParam('tab');
            $backUrl = $this->getUrl(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, "active_tab" => $previousTab]
            );

            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $backUrl . '\')');
            $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
            $this->buttonList->update('save', 'label', __('Save listing update'));
            $this->buttonList->update('save', 'class', 'spectrumButton');
        } catch (NoSuchEntityException $e) {
            // remove reset button
            $this->buttonList->remove('back');
        }
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Amazon Listing Alias Seller SKU');
    }
}
