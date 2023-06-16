<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2021-01-19T20:19:59+00:00
 * File:          Model/Processor/AbstractProcessor.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */
namespace Xtento\TrackingImport\Model\Processor;

use Magento\Framework\DataObject;
use Xtento\TrackingImport\Logger\Logger;
use Xtento\TrackingImport\Model\Processor\Mapping\Fields\Configuration;
use Xtento\TrackingImport\Model\Processor\Mapping\FieldsFactory;

abstract class AbstractProcessor extends DataObject
{
    protected $mappingModel;
    protected $mapping;

    /**
     * @var FieldsFactory
     */
    protected $mappingFieldsFactory;

    /**
     * @var Configuration
     */
    protected $fieldsConfiguration;

    /**
     * @var Logger
     */
    protected $xtentoLogger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * AbstractProcessor constructor.
     *
     * @param FieldsFactory $mappingFieldsFactory
     * @param Configuration $fieldsConfiguration
     * @param Logger $xtentoLogger
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        FieldsFactory $mappingFieldsFactory,
        Configuration $fieldsConfiguration,
        Logger $xtentoLogger,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->mappingFieldsFactory = $mappingFieldsFactory;
        $this->fieldsConfiguration = $fieldsConfiguration;
        $this->xtentoLogger = $xtentoLogger;
        $this->registry = $registry;

        parent::__construct($data);
    }

    protected function getConfiguration()
    {
        return $this->getProfile()->getConfiguration();
    }

    protected function getConfigValue($key)
    {
        $configuration = $this->getConfiguration();
        if (isset($configuration[$key])) {
            return $configuration[$key];
        } else {
            return false;
        }
    }
}