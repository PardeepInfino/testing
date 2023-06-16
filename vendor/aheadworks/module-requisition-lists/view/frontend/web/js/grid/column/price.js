define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'underscore',
    'Aheadworks_RequisitionLists/js/ui/requisition-list/item-listing/item-configurator',
    'Aheadworks_RequisitionLists/js/ui/requisition-list/config'
], function ($, Column, _, itemConfigurator, requisitionListConfig) {
    'use strict';
    return Column.extend({
        defaults: {
            isEditable: 'is_editable',
            isSalableIndex: 'is_salable',
            isAvailableForSiteIndex: 'is_available',
            actionsTmpl: 'Aheadworks_RequisitionLists/ui/grid/column/actions',
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
         * Is salable
         *
         * @return string
         */
        isSalable: function (item) {
            return item[this.isSalableIndex];
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
         * Get options renderer template
         *
         * @return string
         */
        configureItem:function (item) {
            itemConfigurator.configure(item);
        },

        /**
         * Get remove item URL
         *
         * @return string
         */
        getRemoveItemUrl:function (item) {
            return requisitionListConfig.getRemoveItemUrl(item);
        },

        /**
         * Get options renderer template
         *
         * @return string
         */
        removeItem:function (item) {
            $("body").trigger('processStart');
            window.location.replace(requisitionListConfig.getRemoveItemUrl(item));
        }
    });
});
