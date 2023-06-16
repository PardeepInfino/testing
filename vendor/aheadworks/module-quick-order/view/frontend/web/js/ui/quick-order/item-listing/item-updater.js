define([
    'jquery',
    'Aheadworks_QuickOrder/js/action/send-request',
    'Aheadworks_QuickOrder/js/action/show-error-popup',
    'Aheadworks_QuickOrder/js/ui/quick-order/config',
    'Aheadworks_QuickOrder/js/action/update-product-list',
    'Aheadworks_QuickOrder/js/ui/quick-order/message/manager'
], function (
    $,
    sendRequest,
    showError,
    QuickOrderConfig,
    UpdateProductList,
    messageManager
) {
    return {

        /**
         * Update qty
         *
         * @param {Object} itemData
         * @param {Number} qty
         */
        updateQty: function(itemData, qty) {
            var data = {
                'item_key': itemData.item_key,
                'product_qty': qty,
            };

            this._sendRequest(QuickOrderConfig.getUpdateItemQtyUrl(), data);
        },

        /**
         * Remove item
         *
         * @param {String} itemKey
         * @param {Boolean} showMessage
         */
        removeItem: function(itemKey, showMessage = true) {
            this._sendRequest(QuickOrderConfig.getRemoveItemUrl(), {'item_key': itemKey}, showMessage);
        },

        /**
         * Send ajax request to get update item
         *
         * @param {String} url
         * @param {Object} data
         * @param {Boolean} showMessage
         * @private
         */
        _sendRequest: function(url, data, showMessage = true) {
            var self = this;

            $("body").trigger('processStart');
            sendRequest(url, data).done(function(response){
                if (response.error) {
                    self._showError(response.error);
                } else {
                    if (showMessage) {
                        messageManager.showMessages(response.messages);
                    }
                    UpdateProductList();
                }
            }).fail(function(response){
                self._showError(response.statusText);
            }).always(function () {
                $("body").trigger('processStop');
            }.bind(this));
        },

        /**
         * Show error popup
         *
         * @param {String} error
         * @private
         */
        _showError: function(error) {
            showError('We cannot process this request', error);
        }
    };
});
