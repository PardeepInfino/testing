define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'underscore'
], function ($, Column) {
    'use strict';
    return Column.extend({
        defaults: {
            textBeforeUrl: '',
            optionsRendererTemplates: {
                configurable: 'Aheadworks_RequisitionLists/ui/grid/column/renderer/configurable',
                bundle: 'Aheadworks_RequisitionLists/ui/grid/column/renderer/bundle',
                grouped: 'Aheadworks_RequisitionLists/ui/grid/column/renderer/grouped'
            },
            productAttributesIndex: 'product_attributes',
            productTypeIndex: 'product_type',
            productTypeOptionsIndex: 'product_options',
            isAvailableForSiteIndex: 'is_available',
            skuIndex: 'product_sku',
            imageIndex: 'image_html'
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
         * Get Product Name
         *
         * @return string
         */
        getImage: function (item) {
            return item[this.imageIndex];
        },

        /**
         * Get Product SKU
         *
         * @return string
         */
        getSku: function (item) {
            return item[this.skuIndex];
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
         * Get Product Attributes
         *
         * @return string
         */
        getProductAttributes:function (item) {
            var attributes = item[this.productAttributesIndex];

            return attributes[this.productTypeOptionsIndex] || [];
        },

        /**
         * Get options renderer template
         *
         * @return string
         */
        getOptionsRendererTemplate:function (item) {
            return this.optionsRendererTemplates[item[this.productTypeIndex]]
        },

        /**
         * Checking the need to output render options
         *
         * @return boolean
         */
        isOptionsNeed:function (item) {
            return !_.isEmpty(this.optionsRendererTemplates[item[this.productTypeIndex]]);
        }
    });
});

