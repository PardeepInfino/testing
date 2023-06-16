<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:01:20+00:00
 * File:          Helper/Entity.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Helper;

use Magento\Framework\Exception\LocalizedException;
use Xtento\XtCore\Helper\Utils;

class Entity extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var bool
     */
    protected static $magentoMsiSupport = null;

    /**
     * @var \Xtento\TrackingImport\Model\Import
     */
    protected $importModel;

    /**
     * @var Utils
     */
    protected $utilsHelper;

    /**
     * Entity constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Xtento\TrackingImport\Model\Import $importModel
     * @param Utils $utilsHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Xtento\TrackingImport\Model\Import $importModel,
        Utils $utilsHelper
    ) {
        parent::__construct($context);
        $this->importModel = $importModel;
        $this->utilsHelper = $utilsHelper;
    }

    public function getEntityName($entity)
    {
        $entities = $this->importModel->getEntities();
        if (isset($entities[$entity])) {
            return rtrim($entities[$entity], 's');
        } else {
            return __("Undefined Entity");
        }
    }

    public function getPluralEntityName($entity)
    {
        return $entity;
    }

    public function getProcessorName($processor)
    {
        $processors = $this->importModel->getProcessors();
        if (!array_key_exists($processor, $processors)) {
            throw new LocalizedException(__('Processor "%1" does not exist. Cannot load profile.', $processor));
        }
        $processorName = $processors[$processor];
        return $processorName;
    }

    public function getMagentoMSISupport()
    {
        if (self::$magentoMsiSupport !== null) {
            return self::$magentoMsiSupport;
        }

        if (!$this->utilsHelper->isExtensionInstalled('Magento_Inventory')) {
            // Don't use MSI if MSI isn't installed
            self::$magentoMsiSupport = false;
            return self::$magentoMsiSupport;
        }
        self::$magentoMsiSupport = version_compare($this->utilsHelper->getMagentoVersion(), '2.3', '>=');
        return self::$magentoMsiSupport;
    }
}
