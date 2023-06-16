<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Model/System/Config/Source/Import/Processor.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\System\Config\Source\Import;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Processor implements ArrayInterface
{
    /**
     * @var \Xtento\StockImport\Model\Import
     */
    protected $importModel;

    /**
     * Entity constructor.
     *
     * @param \Xtento\StockImport\Model\Import $importModel
     */
    public function __construct(\Xtento\StockImport\Model\Import $importModel)
    {
        $this->importModel = $importModel;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->importModel->getProcessors();
    }
}
