<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\System\Config\Source\Log;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Result implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $values = [
            \Xtento\StockImport\Model\Log::RESULT_NORESULT => __('No Result'),
            \Xtento\StockImport\Model\Log::RESULT_SUCCESSFUL => __('Successful'),
            \Xtento\StockImport\Model\Log::RESULT_WARNING => __('Warning'),
            \Xtento\StockImport\Model\Log::RESULT_FAILED => __('Failed')
        ];
        return $values;
    }
}
