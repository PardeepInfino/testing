<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Block/Adminhtml/Source/Grid/Column/Source.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml\Source\Grid\Column;

class Source extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @var \Xtento\StockImport\Model\ProfileFactory
     */
    protected $profileFactory;

    /**
     * Source constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Xtento\StockImport\Model\ProfileFactory $profileFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Xtento\StockImport\Model\ProfileFactory $profileFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->profileFactory = $profileFactory;
    }

    protected function getProfile()
    {
        return $this->profileFactory->create()->load(
            $this->getRequest()->getParam('id')
        );
    }

    public function getValues()
    {
        $array = [];
        foreach (explode("&", $this->getProfile()->getSourceIds()) as $key => $sourceId) {
            if ($sourceId === '') {
                continue;
            }
            $array[] = ['label' => $sourceId, 'value' => $sourceId];
        }
        return $array;
    }
}
