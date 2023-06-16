<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-11-01T19:16:50+00:00
 * File:          Block/Adminhtml/Profile/Edit/Tab/Settings.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml\Profile\Edit\Tab;

use Xtento\XtCore\Model\System\Config\Source\Order\AllStatuses;

class Settings extends \Xtento\StockImport\Block\Adminhtml\Widget\Tab implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $yesNo;

    /**
     * @var \Xtento\StockImport\Model\System\Config\Source\Product\Identifier
     */
    protected $productIdentifierSource;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $storeSource;

    /**
     * @var AllStatuses
     */
    protected $allStatuses;

    /**
     * @var \Xtento\StockImport\Helper\Entity
     */
    protected $entityHelper;

    /**
     * Settings constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesNo
     * @param \Magento\Store\Model\System\Store $storeSource
     * @param \Xtento\StockImport\Model\System\Config\Source\Product\Identifier $productIdentifierSource
     * @param AllStatuses $allStatuses
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Magento\Store\Model\System\Store $storeSource,
        \Xtento\StockImport\Model\System\Config\Source\Product\Identifier $productIdentifierSource,
        \Xtento\StockImport\Helper\Entity $entityHelper,
        AllStatuses $allStatuses,
        array $data = []
    ) {
        $this->yesNo = $yesNo;
        $this->productIdentifierSource = $productIdentifierSource;
        $this->storeSource = $storeSource;
        $this->entityHelper = $entityHelper;
        $this->allStatuses = $allStatuses;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function getFormMessages()
    {
        $formMessages = [];
        $formMessages[] = [
            'type' => 'notice',
            'message' => __(
                'The settings specified below will be applied to all manual and automatic imports.'
            )
        ];
        return $formMessages;
    }

    /**
     * Prepare form
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('stockimport_profile');
        if (!$model->getId()) {
            return $this;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('settings', ['legend' => __('Import Settings'), 'class' => 'fieldset-wide',]);

        $fieldset->addField(
            'reindex_mode',
            'select',
            [
                'label' => __('Reindex mode'),
                'name' => 'reindex_mode',
                'values' => [
                    ['value' => 'no_reindex', 'label' => __('No reindexing at all. (Recommended)')],
                    ['value' => 'flag_index', 'label' => __('No reindex. Flag as \'reindex required\'.')],
                    ['value' => 'full', 'label' => __('Full (stock table) reindex (after import)')]
                ],
                'note' => __('No reindex is required in most cases.')
            ]
        );

        if ($this->entityHelper->getMagentoMSISupport()) {
            $fieldset->addField(
                'disable_msi_reindex',
                'select',
                [
                    'label' => __('Disable MSI reindex'),
                    'name' => 'disable_msi_reindex',
                    'values' => $this->yesNo->toOptionArray(),
                    'note' => __('If enabled, Magento MSI (Multi-Source Inventory) reindexing after importing will be disabled. Only disable if you are sure what you are doing, and want to use the Magento cron to reindex for example.')
                ]
            );
        }

        $fieldset->addField(
            'invalidate_fpc',
            'select',
            [
                'label' => __('Invalidate Full Page Cache'),
                'name' => 'invalidate_fpc',
                'values' => $this->yesNo->toOptionArray(),
                'note' => __('Do you use the Magento 2 built-in Full Page Cache? If so, enable this so products are invalidated in the full page cache, so that stock status/availability is displayed correctly in the frontend.')
            ]
        );

        $fieldset->addField(
            'mark_out_of_stock',
            'select',
            [
                'label' => __(
                    'Set "In Stock" / "Out of Stock"'
                ),
                'name' => 'mark_out_of_stock',
                'values' => $this->yesNo->toOptionArray(),
                'note' => __('If stock qty is equal or below "Qty for Item\'s Status to become Out of Stock", mark as out of stock (and mark as "In Stock" again if qty is above "Qty for Item\'s Status ..."). This value will be overridden if you map the "Stock Status" field.')
            ]
        );

        $fieldset->addField(
            'import_relative_stock_level',
            'select',
            [
                'label' => __('Import relative stock level'),
                'name' => 'import_relative_stock_level',
                'values' => $this->yesNo->toOptionArray(),
                'note' => __(
                    'If set to "No", the stock level specified in the import file will be imported, whatever it is. If set to "Yes", if you import +5, 5 will be added to the current stock level, and if you import -5, 5 will be subtracted from the current stock level. If you import just "5" as the qty, this will be treated as an absolute stock level and not a relative stock level update, so be sure to prefix your qty with + or -. If you can\'t, check out our wiki for a solution on how to prefix qtys.'
                )
            ]
        );

        $fieldset->addField(
            'product_identifier',
            'select',
            [
                'label' => __('Product Identifier'),
                'name' => 'product_identifier',
                'values' => $this->productIdentifierSource->toOptionArray(),
                'note' => __(
                    'This is what is called the Product Identifier in the import settings and is what\'s used to identify the product in the import file. Almost always you will want to use the SKU.'
                )
            ]
        );
        $attributeCodeJs = "<script>
require([\"jquery\"], function($) {
$(document).ready(function() { 
function checkAttributeField(field) {
    if(field.val()=='attribute') {
        \$('#product_identifier_attribute_code').parent().parent().show()
    } else {
        \$('#product_identifier_attribute_code').parent().parent().hide()
    }
} 
checkAttributeField(\$('#product_identifier')); 
\$('#product_identifier').change(function(){ checkAttributeField($(this)); }); 
});
});
</script>";
        if ($model->getData('product_identifier') !== 'attribute') {
            // Not filled
            $attributeCodeJs .= "<script>
require([\"jquery\"], function($) {
\$('#product_identifier_attribute_code').parent().parent().hide()
});
</script>";
        }
        $fieldset->addField(
            'product_identifier_attribute_code',
            'text',
            [
                'label' => __('Product Identifier: Attribute Code'),
                'name' => 'product_identifier_attribute_code',
                'note' => __(
                        'IMPORTANT: This is not the attribute name. It is the attribute code you assigned to the attribute.'
                    ) . $attributeCodeJs,
            ]
        );

        $fieldset = $form->addFieldset('misc_settings', ['legend' => __('Miscellaneous Settings'), 'class' => 'fieldset-wide']);

        $fieldset->addField('update_low_stock_date', 'select', [
            'label' => __('Update "low stock date" after importing'),
            'name' => 'update_low_stock_date',
            'values' => $this->yesNo->toOptionArray(),
            'note' => __('May make the import slower. Only enable if required.')
        ]);

        $fieldset->addField('adjust_stock_pending_orders', 'select', [
            'label' => __('Adjust stock level by pending orders'),
            'name' => 'adjust_stock_pending_orders',
            'values' => $this->yesNo->toOptionArray(),
            'note' => __('If enabled, when the stock level is imported, the profile will check how often the imported SKU is "blocked" in currently pending/processing orders, and the imported stock level will be reduced by that amount. Beta feature, makes the import slower. Only use it if really required.')
        ]);

        $fieldset->addField('adjust_stock_pending_orders_mode', 'select', [
            'label' => __('Adjust stock level by pending orders: Mode'),
            'name' => 'adjust_stock_pending_orders_mode',
            'values' => [['value' => 1, 'label' => __('Decrease stock level by # of pending orders')], ['value' => 2, 'label' => __('Increase stock level by # of pending orders')]],
            'note' => __('Should the stock level be increased or decreased for "blocked" items in pending orders?')
        ]);

        $fieldset->addField('adjust_stock_pending_orders_statuses', 'multiselect', [
            'label' => __('Adjust stock by pending orders: Statuses'),
            'name' => 'adjust_stock_pending_orders_statuses',
            'values' => $this->allStatuses->toOptionArray(),
            'note' => __('Orders with which order status should be treated as "pending" orders? This is used for the "Adjust stock by pending orders" feature. Default: Pending, Processing')
        ]);

        $fieldset->addField(
            'reset_stock_of_products_not_in_file',
            'select',
            [
                'label' => __(
                    'Reset stock level to 0 for products not in file'
                ),
                'name' => 'reset_stock_of_products_not_in_file',
                'values' => $this->yesNo->toOptionArray(),
                'note' => __('If enabled, the stock level of all your products in Magento will be set to zero before each import. However, only for the products which are not in your import file. Attention, if you have multiple import profiles this setting does not make any sense.')
            ]
        );

        $fieldset->addField(
            'update_parent_product_stock_after_import',
            'select',
            [
                'label' => __(
                    'Update stock status of parent items after import'
                ),
                'name' => 'update_parent_product_stock_after_import',
                'values' => $this->yesNo->toOptionArray(),
                'note' => __('If enabled, after importing, the stock status (in stock/out of stock) of parent items (e.g. configurable products) will be set to in stock/out of stock based on the child products. If one of the child products is in stock, the parent item will be set to in stock as well. Attention, enable only if required - usually not required and makes the import slower.')
            ]
        );

        // Define field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Form\Element\Dependence::class
            )->addFieldMap('adjust_stock_pending_orders_statuses', 'adjust_stock_pending_orders_statuses')
                ->addFieldMap('adjust_stock_pending_orders_mode', 'adjust_stock_pending_orders_mode')
                ->addFieldMap('adjust_stock_pending_orders', 'adjust_stock_pending_orders')
                ->addFieldDependence(
                    'adjust_stock_pending_orders_statuses',
                    'adjust_stock_pending_orders',
                    1
                )->addFieldDependence(
                    'adjust_stock_pending_orders_mode',
                    'adjust_stock_pending_orders',
                    1
                )
        );

        if (\Xtento\StockImport\Model\Import\Entity\Stock::$importPrices || \Xtento\StockImport\Model\Import\Entity\Stock::$importSpecialPrices || \Xtento\StockImport\Model\Import\Entity\Stock::$importCustomAttributes) {
            $fieldset = $form->addFieldset('store', ['legend' => __('Store View (Attribute Update)'), 'class' => 'fieldset-wide']);

            $fieldset->addField('price_update_store_id', 'multiselect', [
                'label' => __('Store View'),
                'name' => 'price_update_store_id',
                'values' => array_merge_recursive([['value' => '', 'label' => __('--- Global (All Store Views) ---')]], $this->storeSource->getStoreValuesForForm()),
                'note' => __('The price and attribute values will be set for the following website/store. Make sure Configuration > Catalog > Price Scope is set to Website if you want to manage prices on a per-website level. Attention: The more stores you select, the slower the import gets. Select only the required stores, or if updating globally, select only the "Global" option.'),
            ]);
        }

        $this->setTemplate('Xtento_StockImport::profile/settings.phtml');

        $configuration = $model->getConfiguration();
        if (empty($configuration)) {
            // Set default values
            $configuration['mark_out_of_stock'] = true;
        }

        if (!isset($configuration['adjust_stock_pending_orders_statuses'])) {
            $configuration['adjust_stock_pending_orders_statuses'] = ['pending', 'processing'];
        }

        $form->setValues($configuration);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Import Settings');
    }

    /**
     * Prepare title for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Import Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}