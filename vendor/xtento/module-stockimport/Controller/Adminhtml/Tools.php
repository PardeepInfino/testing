<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Controller/Adminhtml/Tools.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Controller\Adminhtml;

abstract class Tools extends \Xtento\StockImport\Controller\Adminhtml\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Xtento\StockImport\Model\ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var \Xtento\StockImport\Model\SourceFactory
     */
    protected $sourceFactory;

    /**
     * @var \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface
     */
    protected $requestData;

    /**
     * @var \Xtento\XtCore\Helper\Utils
     */
    protected $utilsHelper;

    /**
     * Tools constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Xtento\StockImport\Helper\Module $moduleHelper
     * @param \Xtento\XtCore\Helper\Cron $cronHelper
     * @param \Xtento\StockImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Xtento\StockImport\Model\ProfileFactory $profileFactory
     * @param \Xtento\StockImport\Model\SourceFactory $sourceFactory
     * @param \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Xtento\StockImport\Helper\Module $moduleHelper,
        \Xtento\XtCore\Helper\Cron $cronHelper,
        \Xtento\StockImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Xtento\StockImport\Model\ProfileFactory $profileFactory,
        \Xtento\StockImport\Model\SourceFactory $sourceFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Xtento\XtCore\Helper\Utils $utilsHelper
    ) {
        parent::__construct($context, $moduleHelper, $cronHelper, $profileCollectionFactory, $scopeConfig);
        $this->profileFactory = $profileFactory;
        $this->sourceFactory = $sourceFactory;
        $this->requestData = $requestData;
        $this->utilsHelper = $utilsHelper;
    }

    /**
     * Check if user has enough privileges
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Xtento_StockImport::tools');
    }

    /**
     * @param $resultPage \Magento\Backend\Model\View\Result\Page
     */
    protected function updateMenu($resultPage)
    {
        $resultPage->setActiveMenu('Xtento_StockImport::tools');
        $resultPage->addBreadcrumb(__('Products'), __('Products'));
        $resultPage->addBreadcrumb(__('Tools'), __('Tools'));
        $resultPage->getConfig()->getTitle()->prepend(__('Stock Import - Tools'));
    }
}
