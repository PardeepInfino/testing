<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-06-24T17:34:33+00:00
 * File:          Model/Processor/Mapping/Fields.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\Processor\Mapping;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Xtento\TrackingImport\Helper\Entity;
use Xtento\XtCore\Model\System\Config\Source\Order\AllStatuses;
use Xtento\XtCore\Model\System\Config\Source\Shipping\Carriers;

class Fields extends AbstractMapping
{
    protected $importFields = null;
    protected $mappingType = 'fields';

    /**
     * @var Entity
     */
    protected $entityHelper;

    /**
     * Fields constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ManagerInterface $eventManager
     * @param Carriers $carriers
     * @param AllStatuses $orderStatuses
     * @param Entity $entityHelper
     * @param array $data
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ManagerInterface $eventManager,
        Carriers $carriers,
        AllStatuses $orderStatuses,
        Entity $entityHelper,
        array $data = []
    ) {
        parent::__construct($objectManager, $eventManager, $carriers, $orderStatuses, $data);
        $this->entityHelper = $entityHelper;
    }


    /*
     * [
     * 'label'
     * 'disabled'
     * 'tooltip'
     * 'default_value_disabled'
     * 'default_values'
     * ]
     */
    public function getMappingFields()
    {
        if ($this->importFields !== null) {
            return $this->importFields;
        }

        $importFields = [
            'order_info' => [
                'label' => __('-- Order Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'order_identifier' => [
                'label' => __('Order Identifier (Order #, ...) *'),
                'default_value_disabled' => true,
                'tooltip' => __(
                    'This is the field used to identify orders in Magento. Usually the order number, but based on the setting in the Import Settings tab, this may be the invoice number for example too.'
                ),
            ],
            'order_status' => [
                'label' => __('Order Status'),
                'default_values' => $this->getDefaultValues('order_status'),
                'tooltip' => __(
                    'This is mapped to the order status set for the order. This must be a valid order status code seen at System > Order Statuses.'
                ),
            ],
            'order_status_history_comment' => [
                'label' => __('Order History Comment'),
                'tooltip' => __('Comment that is added into the order status history after the order was updated.'),
            ],
            /*'invoice_info' => [
                'label' => __('-- Invoice Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],*/
            'shipment_info' => [
                'label' => __('-- Shipment Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'tracking_number' => [
                'label' => __('Tracking Number'),
                'tooltip' => __('The tracking number added to the imported/updated shpiment'),
                'group' => 'tracks'
            ],
            'carrier_code' => [
                'label' => 'Shipping Carrier Code',
                'default_values' => $this->getDefaultValues('shipping_carriers'),
                'tooltip' => __(
                    'The carrier code for the tracking number imported. Make sure this is a valid tracker code, out of the box it is usps, ups, dhl or fedex. If you are using our Custom Carrier Trackers extension, its tracker codes are tracker1 through tracker15.'
                ),
                'group' => 'tracks'
            ],
            'carrier_name' => [
                'label' => __('Shipping Carrier Name'),
                'tooltip' => __('Shipping Carrier Title for tracking number'),
                'group' => 'tracks'
            ],
            'creditmemo_info' => [
                'label' => __('-- Credit Memo Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'creditmemo_shipping_amount' => [
                'label' => __('Credit Memo Shipping Amount'),
                'default_value_disabled' => true,
                'tooltip' => '',
            ],
            'creditmemo_adjustment_positive' => [
                'label' => __('Adjustment Refund'),
                'default_value_disabled' => true,
                'tooltip' => '',
            ],
            'creditmemo_adjustment_negative' => [
                'label' => __('Adjustment Fee'),
                'default_value_disabled' => true,
                'tooltip' => '',
            ],
            'item_info' => [
                'label' => __('-- Item Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'sku' => [
                'label' => __('Product Identifier (SKU, ...)'),
                'default_value_disabled' => true,
                'tooltip' => __('Shipped/Invoiced SKU'),
                'group' => 'items'
            ],
            'qty' => [
                'label' => __('Quantity'),
                'tooltip' => __('Shipped/Invoiced SKU'),
                'group' => 'items'
            ],
            'id_fields' => [
                'label' => __('-- Increment ID Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'invoice_increment_id' => [
                'label' => __('Invoice: increment_id'),
                'tooltip' => __('Force a custom value for the increment_id of the invoice')
            ],
            'shipment_increment_id' => [
                'label' => __('Shipment: increment_id'),
                'tooltip' => __('Force a custom value for the increment_id of the shipment')
            ],
            'date_fields' => [
                'label' => __('-- Date Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'invoice_created_at' => [
                'label' => __('Invoice: created_at (YYYY-MM-DD HH:MM:SS)'),
                'tooltip' => __('Specify the created_at field of the invoice. Must be in format YYYY-MM-DD HH:MM:SS')
            ],
            'shipment_created_at' => [
                'label' => __('Shipment: created_at (YYYY-MM-DD HH:MM:SS)'),
                'tooltip' => __('Specify the created_at field of the shipment. Must be in format YYYY-MM-DD HH:MM:SS')
            ],
            'custom_fields' => [
                'label' => __('-- Custom Fields -- '),
                'disabled' => true,
                'tooltip' => '',
            ],
            'custom1' => [
                'label' => __('Custom Field 1'),
                'tooltip' => __(
                    'Used to store custom data. This can be used to map/check data in the actions tab for example.'
                ),
            ],
            'custom2' => [
                'label' => __('Custom Field 2'),
                'tooltip' => __(
                    'Used to store custom data. This can be used to map/check data in the actions tab for example.'
                ),
            ],
            'custom3' => [
                'label' => __('Custom Field 3'),
                'tooltip' => __(
                    'Used to store custom data. This can be used to map/check data in the actions tab for example.'
                ),
            ],
            'custom4' => [
                'label' => __('Custom Field 4'),
                'tooltip' => __(
                    'Used to store custom data. This can be used to map/check data in the actions tab for example.'
                ),
            ],
            'custom5' => [
                'label' => __('Custom Field 5'),
                'tooltip' => __(
                    'Used to store custom data. This can be used to map/check data in the actions tab for example.'
                ),
            ],
            /*
            'custom_fields' => [
                'label' => '-- Custom Import Fields -- ',
                'disabled' => true
            ],
            //'custom1' => ['label' => 'Custom Data 1'],
            //'custom2' => ['label' => 'Custom Data 2'],
            */
        ];

        if ($this->entityHelper->getMagentoMSISupport()) {
            $importFields['stock_id_settings'] = [
                'label' => __('-- Multi-Source Inventory (MSI) --'),
                'disabled' => true
            ];
            $importFields['source_code'] = [
                'label' => __('Source Code'),
                'default_values' => $this->getDefaultValues('msi_sources'),
                'tooltip' => __(
                    'Specify the source_code of the warehouse from where the shipment will be sent from.'
                ),
            ];
        }

        // Custom event to add fields
        $additional = new \Magento\Framework\DataObject();
        $this->eventManager->dispatch('xtento_trackingimport_mapping_get_fields', ['class' => $this, 'additional' => $additional]);
        $additionalFields = $additional->getFields();
        if ($additionalFields) {
            $importFields = array_merge_recursive($importFields, $additionalFields);
        }

        // Feature: merge fields from custom/fields.php so custom fields can be added

        $this->importFields = $importFields;

        return $this->importFields;
    }

    public function formatField($fieldName, $fieldValue)
    {
        if ($fieldName == 'order_identifier') {
            $fieldValue = trim($fieldValue);
        }
        return $fieldValue;
    }
}
