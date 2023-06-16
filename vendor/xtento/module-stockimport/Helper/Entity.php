<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Helper/Entity.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Helper;

use Magento\Framework\Exception\LocalizedException;

class Entity extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var bool
     */
    protected static $magentoMsiSupport = null;

    /**
     * @var \Xtento\StockImport\Model\Import
     */
    protected $importModel;

    /**
     * @var \Xtento\XtCore\Helper\Utils
     */
    protected $utilsHelper;

    /**
     * Entity constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Xtento\StockImport\Model\Import $importModel
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Xtento\StockImport\Model\Import $importModel,
        \Xtento\XtCore\Helper\Utils $utilsHelper
    ) {
        parent::__construct($context);
        $this->utilsHelper = $utilsHelper;
        $this->importModel = $importModel;
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

    public function getMultiWarehouseSupport()
    {
        if ($this->utilsHelper->isExtensionInstalled('Innoexts_Warehouse')) {
            return true;
        }
        if ($this->utilsHelper->isExtensionInstalled('MDN_AdvancedStock')) {
            return true;
        }
        if ($this->utilsHelper->isExtensionInstalled('Aitoc_Aitquantitymanager')) {
            return true;
        }
        return false;
    }

    public function getMagentoMSISupport()
    {
        if (self::$magentoMsiSupport !== null) {
            return self::$magentoMsiSupport;
        }

        if ($this->getMultiWarehouseSupport() || !$this->utilsHelper->isExtensionInstalled('Magento_Inventory')) {
            // Don't use MSI if one of the multi-warehouse extensions is installed, or if MSI isn't installed
            self::$magentoMsiSupport = false;
            return self::$magentoMsiSupport;
        }
        self::$magentoMsiSupport = version_compare($this->utilsHelper->getMagentoVersion(), '2.3', '>=');
        return self::$magentoMsiSupport;
    }
}
