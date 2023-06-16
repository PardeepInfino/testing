define([
    'jquery',
    'uiComponent',
    'Aheadworks_QuickOrder/js/action/show-error-popup',
    'Aheadworks_QuickOrder/js/action/send-request',
    'Aheadworks_QuickOrder/js/action/update-product-list',
    'Aheadworks_QuickOrder/js/ui/quick-order/message/manager'
], function (
    $,
    Component,
    showError,
    sendRequest,
    updateProductList,
    messageManager
) {
    'use strict';

    return Component.extend({
        defaults: {
            formDataRole: '',
            addToListUrl: '',
        },

        /**
         * Add to list handler
         */
        addToList: function() {
            if (this.validate()) {
                this._sendRequest(this.addToListUrl, this.getData());
            }
        },

        /**
         * Send ajax request
         *
         * @param {String} url
         * @param {Object} data
         * @private
         */
        _sendRequest: function(url, data) {
            var self = this;

            $("body").trigger('processStart');
            sendRequest(url, data)
                .done(this._onRequestSuccess.bind(this))
                .fail(function(response){
                    self._showError(response.statusText);
                }).always(function () {
                    $("body").trigger('processStop');
                }.bind(this));
        },

        /**
         * Update grid with products
         */
        updateProductList: function () {
            updateProductList();
        },

        /**
         * Show messages
         *
         * @param {Array} messages
         */
        showMessages: function (messages) {
            messageManager.showMessages(messages);
        },

        /**
         * Get form data
         */
        getData: function () {
            return {};
        },

        /**
         * Validate form
         *
         * @return {Boolean}
         */
        validate: function () {
            var form = 'form[data-role=' + this.formDataRole + ']';

            return $(form).validation() && $(form).validation('isValid');
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
    });
});
