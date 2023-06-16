<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Model/System/Config/Source/Product/Identifier.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\System\Config\Source\Product;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Identifier implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $identifiers[] = ['value' => 'sku', 'label' => __('SKU')];
        $identifiers[] = ['value' => 'entity_id', 'label' => __('Product ID (entity_id)')];
        $identifiers[] = ['value' => 'attribute', 'label' => __('Custom Product Attribute')];
        return $identifiers;
    }
}
