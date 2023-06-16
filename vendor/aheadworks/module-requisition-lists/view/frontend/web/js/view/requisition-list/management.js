define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'Magento_Customer/js/customer-data'
], function ($, confirm, $t, customerData) {
    'use strict';

    $.widget('awrl.requisitionListManagement', {
        options: {
            deleteButtonSelector: '[data-requisition-list-delete]',
            confirmMsg: $t('Are you sure that you want to delete this Requisition List with all items?'),
            sectionName: 'requisitionlist'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            this._on(this.options.deleteButtonSelector, {
                'click': this._deleteRequisitionList
            });
        },

        /**
         * Delete requisition list
         */
        _deleteRequisitionList: function (e) {
            var self = this;

            e.preventDefault();
            confirm({
                content: self.options.confirmMsg,
                actions: {
                    confirm: function () {
                        customerData.invalidate([self.options.sectionName]);
                        window.location.href = self.options.deleteUrl;
                    }
                }
            });
        }
    });

    return $.awrl.requisitionListManagement;
});