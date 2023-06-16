<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-11-07T20:45:22+00:00
 * File:          Model/Processor/Mapping/Fields.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\Processor\Mapping;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Xtento\StockImport\Helper\Entity;
use \Xtento\StockImport\Model\Import\Entity\Stock;
use Xtento\XtCore\Helper\Utils;

class Fields extends AbstractMapping
{
    protected $importFields = null;
    protected $mappingType = 'fields';

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    private $customerGroupCollectionFactory;

    /**
     * @var Utils
     */
    private $utilsHelper;

    /**
     * Fields constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ManagerInterface $eventManager
     * @param Entity $entityHelper
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     * @param Utils $utilsHelper
     * @param array $data
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ManagerInterface $eventManager,
        Entity $entityHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        Utils $utilsHelper,
        array $data = []
    ) {
        parent::__construct($objectManager, $eventManager, $entityHelper, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeRepository = $attributeRepository;
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->utilsHelper = $utilsHelper;
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
            'item_info' => [
                'label' => __('-- Product Information -- '),
                'disabled' => true
            ],
            'product_identifier' => [
                'label' => __('Product Identifier *'),
                'default_value_disabled' => true
            ],
            'qty' => ['label' => __('Qty In Stock')],
            'stock_settings' => [
                'label' => __('-- Optional: Stock Settings -- '),
                'disabled' => true
            ],
            'manage_stock' => [
                'label' => __('Manage Stock'),
                'default_values' => $this->getDefaultValues('yesno')
            ],
            'use_config_manage_stock' => [
                'label' => __('Manage Stock (Use Config)'),
                'default_values' => $this->getDefaultValues('yesno')
            ],
            'is_in_stock' => [
                'label' => __('Stock Status'),
                'default_values' => $this->getDefaultValues('stock_status')
            ],
            'notify_stock_qty' => [
                'label' => __('Notify Stock Qty (notify_stock_qty)')
            ],
            'backorders' => [
                'label' => __('Backorders'),
                'default_values' => $this->getDefaultValues('backorders')
            ],
            'min_qty' => [
                'label' => __('Out-of-Stock Threshold (min_qty)')
            ],
            'min_sale_qty' => [
                'label' => __('Minimum Qty Allowed in Shopping Cart (min_sale_qty)')
            ],
            'max_sale_qty' => [
                'label' => __('Maximum Qty Allowed in Shopping Cart (max_sale_qty)')
            ],
            /*'use_config_min_qty' => [
                'label' => __('Use Config Settings: Out-of-Stock Threshold'),
                'default_values' => $this->getDefaultValues('yesno')
            ],*/
            /*
            'custom_fields' => array(
                'label' => '-- Custom Import Fields -- ',
                'disabled' => true
            ),
            //'custom1' => array('label' => 'Custom Data 1'),
            //'custom2' => array('label' => 'Custom Data 2'),
            */
        ];


        if (Stock::$importPrices || Stock::$importSpecialPrices || Stock::$importCost) {
            $importFields['price_settings'] = [
                'label' => __('-- Price Import -- '),
                'disabled' => true
            ];
        }

        if (Stock::$importPrices) {
            $importFields['price'] = ['label' => __('Product Price')];
        }

        if (Stock::$importSpecialPrices) {
            $importFields['special_price'] = ['label' => __('Product Special Price')];
        }

        if (Stock::$importCost) {
            $importFields['cost'] = ['label' => __('Product Cost')];
        }

        if (version_compare($this->utilsHelper->getMagentoVersion(), '2.2', '>=')) {
            // Tier price
            $importFields['tier_price:all'] = ['label' => __('Tier Price (All Groups)')];
            foreach ($this->customerGroupCollectionFactory->create() as $customerGroup) {
                $importFields['tier_price:' . $customerGroup->getCustomerGroupId()] = ['label' => __('Tier Price (%1)', $customerGroup->getCustomerGroupCode())];
            }
        }

        if ($this->entityHelper->getMultiWarehouseSupport()) {
            $importFields['stock_id_settings'] = [
                'label' => __('-- Multi-Warehouse (Stock ID) --'),
                'disabled' => true
            ];
            $importFields['stock_id'] = ['label' => __('Stock ID')];
        }

        if ($this->entityHelper->getMagentoMSISupport()) {
            $importFields['stock_id_settings'] = [
                'label' => __('-- Multi-Source Inventory (MSI) --'),
                'disabled' => true
            ];
            $importFields['source_code'] = [
                'label' => __('Source Code'),
                'default_values' => $this->getDefaultValues('msi_sources')
            ];
        }

        if (Stock::$importProductStatus) {
            $importFields['product_settings'] = [
                'label' => __('-- Product Update --'),
                'disabled' => true
            ];
            $importFields['status'] = ['label' => __('Product Status (Enabled/Disabled)')];
        }

        // Custom product attributes
        if (Stock::$importCustomAttributes) {
            $importFields['custom_attributes'] = [
                'label' => __('-- Product Attributes --'),
                'disabled' => true
            ];

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $attributeRepository = $this->attributeRepository->getList(
                'catalog_product',
                $searchCriteria
            );
            $skippedAttributes = ['category_ids', 'image', 'small_image', 'thumbnail', 'tier_price', 'gallery', 'media_gallery', 'image_label', 'small_image_label', 'thumbnail_label', 'quantity_and_stock_status', 'url_key', 'swatch_image', 'sku', 'has_options', 'required_options', 'price_type', 'sku_type', 'weight_type', 'minimal_price', 'custom_design', 'custom_design_from', 'custom_design_to', 'custom_layout_update', 'page_layout', 'samples_title', 'links_title', 'links_purchased_separately', 'custom_layout', 'shipment_type', 'price_view', 'options_container'];
            foreach ($attributeRepository->getItems() as $productAttribute) {
                $attributeCode = $productAttribute->getAttributeCode();
                $frontendLabel = $productAttribute->getFrontendLabel();
                if (array_key_exists($attributeCode, $importFields) || in_array($attributeCode, $skippedAttributes) || empty($frontendLabel)) {
                    continue;
                }
                $importFields['cpa_' . $attributeCode] = ['label' => sprintf("%s [%s]", $productAttribute->getFrontendLabel(), $attributeCode)]; // cpa_ stands for "custom product attribute"
            }
        }

        // Custom event to add fields
        $additional = new \Magento\Framework\DataObject();
        $this->eventManager->dispatch('xtento_stockimport_mapping_get_fields', ['class' => $this, 'additional' => $additional]);
        $additionalFields = $additional->getFields();
        if ($additionalFields) {
            $importFields = array_merge_recursive($importFields, $additionalFields);
        }

        $this->importFields = $importFields;
        return $this->importFields;
    }

    public function formatField($fieldName, $fieldValue)
    {
        if ($fieldName == 'qty') {
            if (!is_numeric($fieldValue)) {
                $fieldValue = trim(strtolower($fieldValue));
                if ($fieldValue === 'yes' || $fieldValue === 'ja' || $fieldValue === 'in stock' || $fieldValue === 'available' || $fieldValue === 'y' || $fieldValue === 'j') {
                    $fieldValue = 1000;
                }
                if ($fieldValue === 'low stock') {
                    $fieldValue = 5;
                }
                if ($fieldValue === 'no' || $fieldValue === 'nein' || $fieldValue === 'out of stock' || $fieldValue === 'no stock' || $fieldValue === 'oos' || $fieldValue === 'n') {
                    $fieldValue = 0;
                }
            }
            if (strval($fieldValue)[0] == '+') {
                $fieldValue = sprintf("%+.4f", $fieldValue);
            } else if (strval($fieldValue)[0] == '-') {
                $fieldValue = str_replace("+", "-", sprintf("%+.4f", $fieldValue)); // Hack as sprintf doesn't support -
            } else {
                $fieldValue = str_replace(",", ".", $fieldValue);
                $fieldValue = preg_replace("/[^0-9.]/", "", $fieldValue);
                $fieldValue = sprintf("%.4f", $fieldValue);
            }
        }
        if ($fieldName == 'is_in_stock' && $fieldValue !== '') {
            if (!is_numeric($fieldValue)) {
                $fieldValue = trim(strtolower($fieldValue));
            }
            // Detect "In Stock" value using this criteria: (is_in_stock in database only accepts 0 or 1)
            if ($fieldValue === 'true' || $fieldValue === 'yes' || $fieldValue === 'ja' || $fieldValue === 'in stock' || $fieldValue === 'available' || (is_numeric($fieldValue) && $fieldValue > 0)
            ) {
                $fieldValue = 1;
            } else {
                $fieldValue = 0;
            }
        }
        if ($fieldName == 'backorders' && $fieldValue !== '') {
            if (!is_numeric($fieldValue)) {
                $fieldValue = trim(strtolower($fieldValue));
            }
            // Detect "In Stock" value using this criteria: (is_in_stock in database only accepts 0 or 1)
            if ($fieldValue === 'true' || $fieldValue === 'yes' || $fieldValue === 'ja' || $fieldValue === '1') {
                $fieldValue = 1;
            } else if ($fieldValue != 2) { // "Notify customer"
                $fieldValue = 0;
            }
        }
        if ($fieldName == 'manage_stock' && $fieldValue !== '') {
            if (!is_numeric($fieldValue)) {
                $fieldValue = trim(strtolower($fieldValue));
            }
            // Detect "Manage Stock" value using this criteria: (manage_stock in database only accepts 0 or 1)
            if ($fieldValue == 'true' || $fieldValue == 'yes' || $fieldValue == 1) {
                $fieldValue = 1;
            } else {
                $fieldValue = 0;
            }
        }
        if ($fieldName == 'price' || $fieldName == 'special_price' || $fieldName == 'cost') {
            if (strstr($fieldValue, '.') && strstr($fieldValue, ',')) {
                // Parse a number in format 1.234,56
                $fieldValue = str_replace('.', '', $fieldValue);
                $fieldValue = str_replace(',', '.', $fieldValue);
            } else if (strstr($fieldValue, ',')) {
                // Parse a number in format 1234,56
                $fieldValue = str_replace(',', '.', $fieldValue);
            }
        }
        if ($fieldName == 'product_identifier') {
            $fieldValue = trim($fieldValue);
        }
        return $fieldValue;
    }
}
