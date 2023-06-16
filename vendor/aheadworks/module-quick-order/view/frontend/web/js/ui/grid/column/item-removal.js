define([
    'Magento_Ui/js/grid/columns/column',
    'Aheadworks_QuickOrder/js/ui/quick-order/item-listing/item-updater'
], function (Column, itemUpdater) {
    'use strict';

    return Column.extend({

        /**
         * Remove item
         *
         * @param {Object} item
         */
        removeItem: function (item) {
            itemUpdater.removeItem(item.item_key);
        }
    });
});
