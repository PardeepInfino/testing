<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2021-07-12T12:19:57+00:00
 * File:          Model/Profile.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model;

use Magento\Framework\Model\AbstractModel;

class Profile extends AbstractModel
{
    /**
     * @var \Xtento\StockImport\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var SourceFactory
     */
    protected $sourceFactory;

    /**
     * @var \Xtento\XtCore\Helper\Cron
     */
    protected $cronHelper;

    /**
     * Profile constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\StockImport\Helper\Module $moduleHelper
     * @param \Xtento\XtCore\Helper\Cron $cronHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param SourceFactory $sourceFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\StockImport\Helper\Module $moduleHelper,
        \Xtento\XtCore\Helper\Cron $cronHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Xtento\StockImport\Model\SourceFactory $sourceFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->cronHelper = $cronHelper;
        $this->request = $request;
        $this->sourceFactory = $sourceFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Xtento\StockImport\Model\ResourceModel\Profile');
        $this->_collectionName = 'Xtento\StockImport\Model\ResourceModel\Profile\Collection';
    }

    public function getSources()
    {
        $sourceIds = array_filter(explode("&", $this->getData('source_ids')));
        $sources = [];
        foreach ($sourceIds as $sourceId) {
            if (!is_numeric($sourceId)) {
                continue;
            }
            $source = $this->sourceFactory->create()->load($sourceId);
            if ($source->getId()) {
                $sources[] = $source;
            }
        }
        // Return sources
        return $sources;
    }

    public function beforeSave()
    {
        // Only call the "rule" model parents _beforeSave function if the profile is modified in the backend, as otherwise the "conditions" ("import filters") could be lost
        if ($this->request->getModuleName() == 'xtento_stockimport' && $this->request->getControllerName(
            ) == 'profile'
        ) {
            parent::beforeSave();
        } else {
            if (!$this->getId()) {
                $this->isObjectNew(true);
            }
        }
        return $this;
    }

    public function afterSave()
    {
        parent::afterSave();
        if ($this->request->getModuleName() == 'xtento_stockimport' && ($this->request->getControllerName() == 'profile' || $this->request->getControllerName() == 'tools')) {
            $this->updateCronjobs();
        }
        if ($this->_registry->registry('xtento_stockimport_update_cronjobs_after_profile_save') !== null) {
            // Can be registered by third party developers, so after they call ->save() on a profile, it will update the profiles cronjobs, added in version 2.1.3
            $this->updateCronjobs();
        }
        return $this;
    }

    public function beforeDelete()
    {
        // Remove existing cronjobs
        $this->cronHelper->removeCronjobsLike('stockimport_profile_' . $this->getId() . '\_%', \Xtento\StockImport\Cron\Import::CRON_GROUP);

        return parent::beforeDelete();
    }

    /**
     * Update database via cron helper
     */
    protected function updateCronjobs()
    {
        // Remove existing cronjobs
        $this->cronHelper->removeCronjobsLike('stockimport_profile_' . $this->getId() . '\_%', \Xtento\StockImport\Cron\Import::CRON_GROUP);

        if (!$this->getEnabled()) {
            return $this; // Profile not enabled
        }
        if (!$this->getCronjobEnabled()) {
            return $this; // Cronjob not enabled
        }

        $cronRunModel = 'Xtento\StockImport\Cron\Import::execute';
        if ($this->getCronjobFrequency(
            ) == \Xtento\StockImport\Model\System\Config\Source\Cron\Frequency::CRON_CUSTOM
            || ($this->getCronjobFrequency() == '' && $this->getCronjobCustomFrequency() !== '')
        ) {
            // Custom cron expression
            $cronFrequencies = $this->getCronjobCustomFrequency();
            if (empty($cronFrequencies)) {
                return $this;
            }
            $cronFrequencies = array_unique(explode(";", $cronFrequencies));
            $cronCounter = 0;
            foreach ($cronFrequencies as $cronFrequency) {
                $cronFrequency = trim($cronFrequency);
                if (empty($cronFrequency)) {
                    continue;
                }
                $cronCounter++;
                $cronIdentifier = 'stockimport_profile_' . $this->getId() . '_cron_' . $cronCounter;
                $this->cronHelper->addCronjob(
                    $cronIdentifier,
                    $cronFrequency,
                    $cronRunModel,
                    \Xtento\StockImport\Cron\Import::CRON_GROUP
                );
            }
        } else {
            // No custom cron expression
            $cronFrequency = $this->getCronjobFrequency();
            if (empty($cronFrequency)) {
                return $this;
            }
            $cronIdentifier = 'stockimport_profile_' . $this->getId() . '_cron';
            $this->cronHelper->addCronjob(
                $cronIdentifier,
                $cronFrequency,
                $cronRunModel,
                \Xtento\StockImport\Cron\Import::CRON_GROUP
            );
        }

        return $this;
    }

    public function saveLastExecutionNow()
    {
        $write = $this->getResource()->getConnection();
        $write->update(
            $this->getResource()->getMainTable(),
            ['last_execution' => (new \DateTime)->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)],
            ["`{$this->getResource()->getIdFieldName()}` = {$this->getId()}"]
        );
    }
}