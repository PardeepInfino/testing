<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-09-02T19:44:11+00:00
 * File:          Controller/Adminhtml/Profile/Edit.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Controller\Adminhtml\Profile;

class Edit extends \Xtento\StockImport\Controller\Adminhtml\Profile
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
        $model = $this->profileFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This profile no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(
                    \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
                );
                return $resultRedirect->setPath('*/*/');
            }
            if (!$model->getEntity()) {
                $this->messageManager->addErrorMessage(__('No import entity has been set for this profile.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(
                    \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
                );
                return $resultRedirect->setPath('*/*/');
            }
        }

        $session = $this->_session;
        $data = $session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->registry->unregister('stockimport_profile');
        $this->registry->register('stockimport_profile', $model);

        // Add check if spreadsheets library is installed
        if ($model->getProcessor() == \Xtento\StockImport\Model\Import::PROCESSOR_SPREADSHEET && !class_exists('\PhpOffice\PhpSpreadsheet\Reader\Xls')) {
            $this->messageManager->addErrorMessage(
                __(
                    'WARNING: You are trying to import data from a spreadsheet, but did not install the spreadsheet library. The import won\'t work. Please install the phpoffice/phpspreadsheet library as explained in our wiki using composer in order to use this import processor.'
                )
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);

        if ($this->registry->registry('stockimport_profile') &&
            $this->registry->registry('stockimport_profile')->getId()
        ) {
            $resultPage->getConfig()->getTitle()->prepend(
                __(
                    'Edit Import Profile \'%1\'',
                    $this->escaper->escapeHtml($this->registry->registry('stockimport_profile')->getName())
                )
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Profile'));
        }

        if ($session->getProfileDuplicated()) {
            $session->setProfileDuplicated(0);
        }

        return $resultPage;
    }
}