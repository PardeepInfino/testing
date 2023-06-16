define([
    'Magento_Ui/js/grid/columns/column',
    'underscore'
], function (Column, _) {
    'use strict';
    return Column.extend({
        defaults: {
            productNamesIndex: 'product_name'
        },

        /**
         * Get Product Url
         *
         * @return string
         */
        getUrl: function (item) {
            return item[this.productNamesIndex + '_url'];
        },

        /**
         * Get Image Label
         *
         * @return string
         */
        getlabel: function (item) {
            return item[this.index + '_label'];
        },

        /**
         * Get Image Url
         *
         * @return string
         */
        getImageUrl: function (item) {
            return item[this.index + '_url'];
        }
    });
});
