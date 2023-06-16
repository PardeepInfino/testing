define([
    'Aheadworks_QuickOrder/js/ui/quick-order/toolbar/tab/abstract-form',
    'jquery',
], function (AbstractForm, $) {
    'use strict';

    return AbstractForm.extend({
        defaults: {
            formDataRole: 'aw-qo-import-sku-form',
            downloadSampleFileUrl: '',
        },

        /**
         * On send request success handler
         *
         * @param {Object} response
         * @private
         */
        _onRequestSuccess: function(response) {
            if (response.error) {
                this._showError(response.error);
            } else {
                this.showMessages(response.messages);
                if (response.success_messages.length) {
                    this.updateProductList();
                }
            }
        },

        /**
         * @inheritdoc
         */
        getData: function () {
            var form = $('form[data-role=' + this.formDataRole + ']');

            return new FormData(form[0]);
        }
    });
});
