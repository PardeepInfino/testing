<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Controller/Adminhtml/Source/Edit.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Controller\Adminhtml\Source;

class Edit extends \Xtento\StockImport\Controller\Adminhtml\Source
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
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

        $id = $this->getRequest()->getParam('id');
        $model = $this->sourceFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This source no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(
                    \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
                );
                return $resultRedirect->setPath('*/*/');
            }
            if ($model->getType() == \Xtento\StockImport\Model\Source::TYPE_LOCAL) {
                if (!$model->getPath()) {
                    $model->setPath('./var/stockimport/');
                    $model->setArchivePath('./var/stockimport/archive/');
                }
            }
        }

        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->registry->unregister('stockimport_source');
        $this->registry->register('stockimport_source', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);

        if ($this->registry->registry('stockimport_source') && $this->registry->registry(
                'stockimport_source'
            )->getId()
        ) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Source'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Source'));
        }

        return $resultPage;
    }
}