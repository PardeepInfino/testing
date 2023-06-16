define([
    'jquery',
    'requisitionListAddTo'
], function ($, Component) {
    'use strict';

    return Component.extend({

        /**
         * Order add
         */
        _orderAdd: function () {
            var self = this,
                data = {order_id: this.orderId, 'list_id':this.listId}
            return $.ajax({
                url: this.addToRequisitionListUrl,
                type: 'post',
                data: data,
                beforeSend: function() {
                    $('body').trigger('processStart');
                },
                success: function(response) {
                    if (response && typeof response == 'object') {
                        self.redirect(response.backUrl);
                    }
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
                this._orderAdd();
            }
        },
    });
});
