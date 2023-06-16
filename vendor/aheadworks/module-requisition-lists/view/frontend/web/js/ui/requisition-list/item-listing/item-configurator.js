define([
    'jquery',
    'Aheadworks_RequisitionLists/js/action/send-request',
    'Aheadworks_RequisitionLists/js/action/show-error-popup',
    'Aheadworks_RequisitionLists/js/ui/requisition-list/config',
    'Aheadworks_RequisitionLists/js/action/update-product-list',
    'Magento_Ui/js/modal/modal'
], function (
    $,
    sendRequest,
    showError,
    RequisitionListsConfig,
    UpdateProductList,
    modal
) {
    return {

        /**
         * Original item post data to configure popup
         */
        itemId: null,

        /**
         * Popup container ID
         */
        popupContainer: '#aw-rl-item-configuration-popup',

        /**
         * Form with product options
         */
        configurationForm: '#aw-rl-configure-item-form',

        /**
         * Configure item
         *
         * @param {Object} itemData
         */
        configure: function(itemData) {
            this.itemId = itemData.item_id;
            this._sendRequest(RequisitionListsConfig.getConfigureItemUrl(), {'item_id': this.itemId});
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
            sendRequest(url, data)
                .done(function(response){
                    if (response.error) {
                        self._showError(response.error);
                    }
                    if (response.content) {
                        self._showPopup(response.title, response.content);
                    }
                })
                .fail(function(response){
                    showError(errorTitle, response.statusText);
                })
                .always(function () {
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
                    modalClass: 'rl-configurator-popup',
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
                formData.append('item_id', this.itemId);

                this._updateItem(formData);

                $(this.popupContainer).modal('closeModal');
                $(this.popupContainer).html("");
            }
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
            sendRequest(RequisitionListsConfig.getUpdateItemOptionUrl(), data).done(function(response){
                if (response.error) {
                    self._showError(response.error);
                } else {
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
