<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2016-03-05T13:40:03+00:00
 * File:          Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\System\Config\Source\Log;

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
            \Xtento\TrackingImport\Model\Log::RESULT_NORESULT => __('No Result'),
            \Xtento\TrackingImport\Model\Log::RESULT_SUCCESSFUL => __('Successful'),
            \Xtento\TrackingImport\Model\Log::RESULT_WARNING => __('Warning'),
            \Xtento\TrackingImport\Model\Log::RESULT_FAILED => __('Failed')
        ];
        return $values;
    }
}
