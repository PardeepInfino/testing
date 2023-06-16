<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Block/Adminhtml/Tools/Export.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml\Tools;

class Export extends \Magento\Backend\Block\Template
{
    /**
     * @var \Xtento\StockImport\Model\ResourceModel\Source\CollectionFactory
     */
    protected $sourceCollectionFactory;

    /**
     * @var \Xtento\StockImport\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Xtento\StockImport\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory
     * @param \Xtento\StockImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Xtento\StockImport\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory,
        \Xtento\StockImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        $this->profileCollectionFactory = $profileCollectionFactory;
    }

    public function getProfiles()
    {
        $profileCollection = $this->profileCollectionFactory->create();
        $profileCollection->getSelect()->order('name ASC');
        return $profileCollection;
    }

    public function getSources()
    {
        $sourceCollection = $this->sourceCollectionFactory->create();
        $sourceCollection->getSelect()->order('name ASC');
        return $sourceCollection;
    }
}
