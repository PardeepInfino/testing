define([
    'jquery',
    'Aheadworks_QuickOrder/js/action/send-request',
    'Aheadworks_QuickOrder/js/action/show-error-popup',
    'Aheadworks_QuickOrder/js/ui/quick-order/config',
    'Aheadworks_QuickOrder/js/action/update-product-list',
    'Aheadworks_QuickOrder/js/ui/quick-order/item-listing/item-updater',
    'Magento_Ui/js/modal/modal',
    'Aheadworks_QuickOrder/js/ui/quick-order/message/manager'
], function (
    $,
    sendRequest,
    showError,
    QuickOrderConfig,
    UpdateProductList,
    itemUpdater,
    modal,
    messageManager
) {
    return {

        /**
         * Original item post data to configure popup
         */
        itemKey: null,

        /**
         * Remove item on popup close
         */
        removeOnClose: false,

        /**
         * Popup container ID
         */
        popupContainer: '#aw-qo-item-configuration-popup',

        /**
         * Form with product options
         */
        configurationForm: '#aw-qo-configure-item-form',

        /**
         * Configure item
         *
         * @param {String} itemKey
         * @param {Boolean} removeOnClose
         */
        configure: function(itemKey, removeOnClose = false) {
            this.itemKey = itemKey;
            this.removeOnClose = removeOnClose;
            this._sendRequest(QuickOrderConfig.getConfigureItemUrl(), {'item_key': this.itemKey});
        },

        /**
         * Send ajax request to get item configuration
         *
         * @param {String} url
         * @param {Object} data
         * @private
         */
        _sendRequest: function(url, data) {
            var self = this,
                errorTitle = 'We cannot process this request';

            $("body").trigger('processStart');
            sendRequest(url, data).done(function(response){
                if (response.error) {
                    self._showError(response.error);
                }
                if (response.content) {
                    self._showPopup(response.title, response.content);
                }
            }).fail(function(response){
                showError(errorTitle, response.statusText);
            }).always(function () {
                $("body").trigger('processStop');
            }.bind(this));
        },

        /**
         * Show popup with item options to configure
         *
         * @param {String} popupTitle
         * @param {String} popupContent
         * @private
         */
        _showPopup: function(popupTitle, popupContent) {
            var popup = $(this.popupContainer),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: popupTitle,
                    modalClass: 'qo-configurator-popup',
                    modalCloseBtnHandler: this._onCancel.bind(this),
                    clickableOverlay: false,
                    keyEventHandlers: {
                        escapeKey: function () {}
                    },
                    buttons: [{
                        text: $.mage.__('Confirm'),
                        class: 'action confirm',
                        click: this._onConfirmClick.bind(this)
                    }]
                };
            modal(options, popup);
            popup.html(popupContent);
            popup.modal('openModal');
            popup.trigger('contentUpdated');
        },

        /**
         * On confirm click button handler
         *
         * @private
         */
        _onConfirmClick: function() {
            var form = $(this.configurationForm),
                formData = new FormData(form[0]);

            if (form.validation('isValid')) {
                formData.append('item_key', this.itemKey);

                this._updateItem(formData);

                $(this.popupContainer).modal('closeModal');
                $(this.popupContainer).html("");
            }
        },

        /**
         * On cancel button handler
         *
         * @private
         */
        _onCancel: function() {
            if (this.removeOnClose) {
                itemUpdater.removeItem(this.itemKey, false);
            }
            $(this.popupContainer).modal('closeModal');
        },

        /**
         * Update product list item
         *
         * @param {Object} data
         * @private
         */
        _updateItem: function(data) {
            var self = this;

            $("body").trigger('processStart');
            sendRequest(QuickOrderConfig.getUpdateItemOptionUrl(), data).done(function(response){
                if (response.error) {
                    self._showError(response.error);
                } else {
                    messageManager.showMessages(response.messages);
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
