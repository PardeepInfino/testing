define([
    'Aheadworks_QuickOrder/js/ui/quick-order/toolbar/tab/abstract-form',
], function (AbstractForm) {
    'use strict';

    return AbstractForm.extend({
        defaults: {
            formDataRole: 'aw-qo-multiple-sku-form',
            skuListValue: '',
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe(['skuListValue']);

            return this;
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
                    this.skuListValue('');
                }
            }
        },

        /**
         * @inheritdoc
         */
        getData: function () {
            return {
                'sku_list': this.skuListValue(),
            };
        }
    });
});
