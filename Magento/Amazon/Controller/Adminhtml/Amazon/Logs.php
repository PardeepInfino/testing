<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Logs extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates main account grid
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var PageFactory */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Amazon::channel_amazon_index');
        $resultPage->getConfig()->getTitle()->prepend(__('Amazon Sales Channel Logs'));

        return $resultPage;
    }
}
