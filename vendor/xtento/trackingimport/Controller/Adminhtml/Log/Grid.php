<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2018-08-11T12:51:00+00:00
 * File:          Controller/Adminhtml/Log/Grid.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml\Log;

class Grid extends \Xtento\TrackingImport\Controller\Adminhtml\Log
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $healthCheck = $this->healthCheck();
        if ($healthCheck !== true) {
            $resultRedirect = $this->resultFactory->create(
                \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
            );
            return $resultRedirect->setPath($healthCheck);
        }

        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_LAYOUT);
        return $resultPage;
    }
}
