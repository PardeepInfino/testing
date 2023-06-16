define([
    'jquery',
    'Aheadworks_QuickOrder/js/ui/quick-order/toolbar/tab/abstract-form',
    'Aheadworks_QuickOrder/js/ui/quick-order/item-listing/item-configurator',
    'Aheadworks_QuickOrder/js/widget/autocomplete',
    'Aheadworks_QuickOrder/js/ui/quick-order/config'
], function ($, AbstractForm, itemConfigurator, autoComplete, quickOrderConfig) {
    'use strict';

    return AbstractForm.extend({
        defaults: {
            formDataRole: 'aw-qo-single-sku-form',
            searchInput: 'single-search-input',
            minLength: 3,
            searchUrl: '',
            singleSearchValue: '',
            singleQtyValue: '1',
            isQtyFieldVisible: quickOrderConfig.isQtyInputDisplayed(),
            isAddToListButtonVisible: quickOrderConfig.isAddToListButtonDisplayed()
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe(['singleSearchValue', 'singleQtyValue']);

            return this;
        },

        /**
         * Initialize auto complete
         */
        initAutoComplete: function () {
            autoComplete({
                "minLength": this.minLength,
                "searchUrl": this.searchUrl,
                "select": function (event, ui) {
                    this.onSelectItem(ui);
                    return false;
                }.bind(this),
                "focus": function (event, ui) {
                    if (ui.item.sku) {
                        $('.ui-autocomplete .ui-menu-item').removeClass("item-focus");
                        $('.ui-autocomplete .ui-state-focus').parent().addClass("item-focus");
                        this.singleSearchValue(ui.item.sku);
                    }
                    return false;
                }.bind(this)
            }, $("#" + this.searchInput));
        },

        /**
         * On select item handler
         *
         * @param {Object} ui
         */
        onSelectItem: function (ui) {
            this.singleSearchValue(ui.item.sku);
            if (!this.isAddToListButtonVisible) {
                this.addToList();
            }
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
                if (response.is_editable) {
                    itemConfigurator.configure(response.last_added_item_key, true);
                    this.singleSearchValue('');
                    this.singleQtyValue(1);
                } else {
                    this.showMessages(response.messages);
                    if (response.success_messages.length) {
                        this.updateProductList();
                        this.singleSearchValue('');
                        this.singleQtyValue(1);
                    }
                }
            }
        },

        /**
         * @inheritdoc
         */
        getData: function () {
            return {
                'product_sku': this.singleSearchValue(),
                'product_qty': this.singleQtyValue(),
            };
        }
    });
});
