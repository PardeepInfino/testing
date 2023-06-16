<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Controller/Adminhtml/Source/NewAction.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Controller\Adminhtml\Source;

class NewAction extends \Xtento\StockImport\Controller\Adminhtml\Source
{
    /**
     * Forward to edit
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $result->forward('edit');
    }
}