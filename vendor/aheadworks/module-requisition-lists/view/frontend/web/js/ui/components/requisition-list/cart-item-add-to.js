define([
    'jquery',
    'requisitionListAddTo'
], function ($, Component) {
    'use strict';

    return Component.extend({

        /**
         * add item from cart
         */
        _addItemFromCart: function () {
            var data = this.getDataAjax();

            return $.ajax({
                url: this.addToRequisitionListUrl,
                type: 'post',
                data: data,
                beforeSend: function() {
                    $('body').trigger('processStart');
                },
                complete: function() {
                    $('body').trigger('processStop');
                }
            });
        },

        /**
         * Button action
         */
        applyAction: function(listData) {
            this.listId = listData.list_id;
            if (!_.isEmpty(this.addToRequisitionListUrl)) {
                this._addItemFromCart();
            }
        },

        /**
         * Get data ajax
         */
        getDataAjax()
        {
            var data = {'list_id': this.listId};

            if (this.productId) {
                data.product_id = this.productId;
            }

            return data;
        }
    });
});
