define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/lib/validation/validator',
    'Aheadworks_QuickOrder/js/ui/quick-order/item-listing/item-updater'
], function ($, Column, validator, itemUpdater) {
    'use strict';

    return Column.extend({
        defaults: {
            selector: {
                row: ".aw-qo__item-listing  .data-row",
                error: "#field-error"
            },
            validateRule: 'validate-item-quantity',
            isQtyEditableIndex: 'is_qty_editable',
            qtySalableMessage: 'qty_salable_message',
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
         * Check if quantity field is disabled
         *
         * @param {array} item
         * @returns {Boolean}
         */
        isQtyFieldDisabled: function (item) {
            return !Boolean(item[this.isQtyEditableIndex]);
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
         * Get quantity salable message
         *
         * @param {array} item
         * @returns {Boolean}
         */
        getQtySalableMessage: function (item) {
            return item[this.qtySalableMessage];
        },

        /**
         * On quantity change handler
         *
         * @param {Object} item
         * @param {Object} elem
         * @param {Object} event
         */
        onQtyChange: function (item, elem, event) {
            var qty = $(event.target).val(),
                result = validator(this.validateRule, qty, {});

            if (result.message) {
                this._addQtyError(this.getItemId(item), result.message);
            } else {
                this._removeQtyError(this.getItemId(item));
                itemUpdater.updateQty(item, qty);
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
