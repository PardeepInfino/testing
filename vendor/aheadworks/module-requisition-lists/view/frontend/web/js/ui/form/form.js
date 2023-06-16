define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/customer-data'
], function ($, _, Component, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            sectionName: 'requisitionlist'
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                ._addFormKeyIfNotSet();

            return this;
        },

        /**
         * {@inheritdoc}
         */
        submit: function(redirect) {
            customerData.invalidate([this.sectionName]);
            this._super();
        },

        /**
         * Add form key to window object if form key is not added earlier
         * Used for submit request validation
         *
         * @returns {Form} Chainable
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return this;
        }
    });
});
