define([
    'Magento_Ui/js/grid/massactions',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
], function (Component, utils, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            isActionMenuVisible: false,
            listens: {
                '${ $.provider }:reloaded': 'onDataReloaded'
            },
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe('isActionMenuVisible');

            return this;
        },

        /**
         * @inheritdoc
         */
        applyAction: function (actionIndex) {
            var action = this.getAction(actionIndex);

            if (this._isEmptyDataSource()) {
                alert({
                    content: this.noItemsMsg
                });

                return this;
            }

            utils.submit({
                url: action.url,
                data: {
                    'action_type': action.type
                }
            });

            return this;
        },

        /**
         * Listener of the items provider children array changes
         */
        onDataReloaded: function () {
            this.isActionMenuVisible(!this._isEmptyDataSource());
        },

        /**
         * Check is empty data source
         *
         * @private
         */
        _isEmptyDataSource: function () {
            return !Boolean(this.source.data.items.length);
        },
    });
});