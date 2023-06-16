define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/lib/validation/validator',
    'uiRegistry'
], function ($, Column, validator, registry) {
    'use strict';

    return Column.extend({
        defaults: {
            multiselectElement: 'aw_requisition_list_item_listing.' +
                'aw_requisition_list_item_listing.' +
                'aw_requisition_list_item_columns.' +
                'ids',
            selector: {
                row: ".customer_list_item .data-row",
                error: "#field-error",
                qty: '#qty'
            },
            validateRule: 'validate-item-quantity',
            isEnabledIndex: 'is_qty_enabled',
            isOutOfStockIndex: 'is_out_of_stock',
            isItemDisabledIndex: 'is_disabled'
        },

        /**
         * Get item ID
         *
         * @param {array} item
         * @returns {string}
         */
        getItemId: function (item) {
            return item['item_id'];
        },

        /**
         * Get qty selector
         *
         * @param {int} itemId
         * @returns {string}
         */
        getQtySelector: function (itemId) {
            return this.selector.row + ' ' + this.selector.qty + itemId;
        },


        /**
         * Get Qty
         *
         * @param {int} itemId
         * @returns {string}
         */
        getQty: function (itemId) {
            return $(this.getQtySelector(itemId)).val();
        },

        /**
         * Is field enabled
         *
         * @param {array} item
         * @returns {boolean}
         */
        isEnabled: function (item) {
            return item[this.isEnabledIndex];
        },

        /**
         * Is item in stock
         *
         * @param item
         * @returns {boolean}
         */
        isOutOfStock: function (item) {
            return item[this.isOutOfStockIndex] && !item[this.isItemDisabledIndex];
        },

        /**
         * Get product quantity
         *
         * @param {array} item
         * @returns {int}
         */
        getProductQty: function (item) {
            return Number(item[this.index]);
        },

        /**
         * On quantity change handler
         *
         * @param {Object} item
         * @param {Object} elem
         * @param {Object} event
         */
        onQtyChange: function (item, elem, event) {
            var multiselectColumn = registry.get(this.multiselectElement),
                qty = $(event.target).val(),
                result = validator(this.validateRule, qty, {});

            if (result.message) {
                this._addQtyError(this.getItemId(item), result.message);
            } else {
                this._removeQtyError(this.getItemId(item));
                multiselectColumn.select(this.getItemId(item), false);
                multiselectColumn.updateLocalStorage();
            }
        },

        /**
         * Add error message
         *
         * @param {Number} itemId
         * @param {String} message
         * @private
         */
        _addQtyError: function (itemId, message) {
            var errorSelector = this._getErrorSelector(itemId);

            $(errorSelector).html(message);
        },

        /**
         * Remove qty error
         *
         * @param {Number} itemId
         * @private
         */
        _removeQtyError: function (itemId) {
            var errorSelector = this._getErrorSelector(itemId);

            $(errorSelector).html('');
        },

        /**
         * Validate qty value
         *
         * @param {string} qty
         * @return {array}
         */
        validate: function (qty) {
            return validator(this.validateRule, qty, {});
        },

        /**
         * Get error selector
         *
         * @param {Number} itemId
         * @returns {String}
         * @private
         */
        _getErrorSelector: function (itemId) {
            return this.selector.row + ' ' + this.selector.error + itemId;
        }
    });
});
