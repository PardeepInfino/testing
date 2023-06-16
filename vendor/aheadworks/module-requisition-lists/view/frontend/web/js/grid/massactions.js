define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/massactions'
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            qtyElement: 'aw_requisition_list_item_listing.' +
                'aw_requisition_list_item_listing.' +
                'aw_requisition_list_item_columns.' +
                'product_qty',
            storageKey: 'aheadworks_requisitionlists_selected_product',
            imports: {
                visible: '${ $.provider }:data.totalRecords'
            },
            tracks: {
                visible: true
            }
        },

        /**
         * {@inheritDoc}
         */
        defaultCallback: function (action, data) {
            var qtyInfo = [],
                storageSelected = [],
                validFlag = true;

            if (window.localStorage.getItem(this.storageKey)) {
                storageSelected = JSON.parse(window.localStorage.getItem(this.storageKey));
            }

            $.each(storageSelected, function (indexRow, item) {
                if (item.isValid) {
                    qtyInfo[item.id] = item.qty;
                } else {
                    validFlag = false;
                }
            });

            if (validFlag) {
                _.extend(data.params, {'qty': qtyInfo});
                this._addFormKeyIfNotSet();
                this._super();
            }
        },

        /**
         * Add form key to window object if form key is not added earlier
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
        }
    });
});
