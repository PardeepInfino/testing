<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-03-03T22:01:39+00:00
 * File:          Model/System/Config/Source/Product/Identifier.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\System\Config\Source\Product;

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
        $identifiers[] = ['value' => 'order_item_id', 'label' => __('Order Item ID (order_item_id)')];
        $identifiers[] = ['value' => 'attribute', 'label' => __('Custom Product Attribute')];
        return $identifiers;
    }
}
