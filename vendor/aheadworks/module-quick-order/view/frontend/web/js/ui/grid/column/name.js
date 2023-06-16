define([
    'Magento_Ui/js/grid/columns/column',
    'underscore',
    'Aheadworks_QuickOrder/js/ui/quick-order/item-listing/item-configurator'
], function (Column, _, itemConfigurator) {
    'use strict';
    return Column.extend({
        defaults: {
            textBeforeUrl: '',
            optionsRendererTemplates: {
                default: 'Aheadworks_QuickOrder/ui/grid/column/renderer/configurable',
                bundle: 'Aheadworks_QuickOrder/ui/grid/column/renderer/bundle',
                grouped: 'Aheadworks_QuickOrder/ui/grid/column/renderer/grouped',
            },
            productAttributesIndex: 'product_attributes',
            productTypeOptionsIndex: 'product_options',
            productTypeCustomOptionsIndex: 'custom_options',
            productTypeIndex: 'product_type',
            editUrlIndex: 'edit_url',
            isSalableIndex: 'is_salable',
            isAvailableForSiteIndex: 'is_available',
            isEditable: 'is_editable',
            preparationError: 'preparation_error',
            isAvailableForQuickOrderIndex: 'is_available_for_quick_order'
        },

        /**
         * Get Product Url
         *
         * @return string
         */
        getUrl: function (item) {
            return item[this.index + '_url'];
        },

        /**
         * Get Product Name
         *
         * @return string
         */
        getName: function (item) {
            return item[this.index];
        },

        /**
         * Get product preparation error
         *
         * @return string
         */
        getPreparationError: function (item) {
            return item[this.preparationError];
        },

        /**
         * Is salable
         *
         * @return string
         */
        isSalable: function (item) {
            return item[this.isSalableIndex];
        },

        /**
         * Is available
         *
         * @return string
         */
        isEditAvailable: function (item) {
            return this.isSalable(item) && item[this.isEditable] && this.isAvailableForSite(item);
        },

        /**
        * Is visibility in site
        *
        * @return boolean
        */
        isAvailableForSite: function (item) {
            return item[this.isAvailableForSiteIndex];
        },

        /**
         * Is available for quick order
         *
         * @return boolean
         */
        isAvailableForQuickOrder: function (item) {
            return item[this.isAvailableForQuickOrderIndex];
        },

        /**
         * Get Product Attributes
         *
         * @return string
         */
        getProductAttributes:function (item) {
            var attributes = item[this.productAttributesIndex];
            var options = attributes[this.productTypeOptionsIndex] || [];

            if (typeof attributes[this.productTypeCustomOptionsIndex] !== "undefined") {
                options = options.concat(attributes[this.productTypeCustomOptionsIndex]);
            }

            return options;
        },

        /**
         * Get options renderer template
         *
         * @return string
         */
        getOptionsRendererTemplate:function (item) {
            return this.optionsRendererTemplates[item[this.productTypeIndex]]
                ? this.optionsRendererTemplates[item[this.productTypeIndex]]
                : this.optionsRendererTemplates['default'];
        },

        /**
         * Get options renderer template
         *
         * @return string
         */
        configureItem:function (item) {
            itemConfigurator.configure(item.item_key);
        },

        /**
         * Checking the need to output render options
         *
         * @return {Boolean}
         */
        isOptionsRequired:function (item) {
            return Boolean(item[this.isEditable]);
        }
    });
});
