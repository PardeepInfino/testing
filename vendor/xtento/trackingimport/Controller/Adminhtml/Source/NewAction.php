<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2016-03-13T19:37:15+00:00
 * File:          Controller/Adminhtml/Source/NewAction.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml\Source;

class NewAction extends \Xtento\TrackingImport\Controller\Adminhtml\Source
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