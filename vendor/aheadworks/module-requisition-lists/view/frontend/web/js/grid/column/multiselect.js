define([
    'Magento_Ui/js/grid/columns/multiselect',
    'uiRegistry',
], function (Multiselect, registry) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            qtyElement: 'aw_requisition_list_item_listing.' +
                'aw_requisition_list_item_listing.' +
                'aw_requisition_list_item_columns.' +
                'product_qty',
            storageKey: 'aheadworks_requisitionlists_selected_product',
            storageSelected: []
        },

        /**
         * {@inheritDoc}
         */
        initialize: function () {
            this._super();
            window.localStorage.removeItem(this.storageKey);

            return this;
        },

        /**
         * {@inheritDoc}
         */
        updateState: function () {
            this.updateLocalStorage();

            this._super();
        },

        /**
         * Update data in window.localStorage.
         */
        updateLocalStorage: function () {
            var selected = this.selected(),
                excluded = this.excluded();

            this._getStorageItem();
            this._processSelected(selected);
            this._processExcluded(excluded);
            this._setStorageItem();
        },

        /**
         * Get data from window.localStorage.
         */
        _getStorageItem: function() {
            var localStorage = window.localStorage;

            if (localStorage.getItem(this.storageKey)) {
                this.storageSelected = JSON.parse(localStorage.getItem(this.storageKey));
            }
        },

        /**
         * Add or update selected records.
         *
         * @param {Array} selected - current selected records
         */
        _processSelected: function(selected) {
            var qty,
                currentDataObj,
                indexOfStorageItem,
                validateResult,
                self = this,
                qtyColumn = registry.get(this.qtyElement);

            selected.map(function(selectedItem) {
                qty = qtyColumn.getQty(selectedItem);
                if (qty) {
                    validateResult = qtyColumn.validate(qty);
                    currentDataObj = {
                        id: selectedItem,
                        qty: qty,
                        isValid: validateResult.passed
                    };
                    indexOfStorageItem = self.storageSelected.findIndex(function(storageItem) {
                        return storageItem.id === selectedItem;
                    });

                    if (indexOfStorageItem !== -1) {
                        self.storageSelected[indexOfStorageItem] = currentDataObj;
                    } else {
                        self.storageSelected.push(currentDataObj);
                    }
                }
            });
        },

        /**
         * Remove selected records.
         *
         * @param {Array} excluded - current excluded records
         */
        _processExcluded: function(excluded) {
            var indexOfExcludedItem,
                self = this;

            excluded.map(function(excludedItem) {
                indexOfExcludedItem = self.storageSelected.findIndex(function(storageItem) {
                    return storageItem.id === excludedItem;
                });

                if (indexOfExcludedItem !== -1) {
                    self.storageSelected.splice(indexOfExcludedItem, 1);
                }
            });
        },

        /**
         * Set data into window.localStorage.
         */
        _setStorageItem: function() {
            window.localStorage.setItem(this.storageKey, JSON.stringify(this.storageSelected));
        }
    });
});

