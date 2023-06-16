define([
    'jquery',
    'requisitionListAddTo'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            productType: '',
            excludedTypes: ['bundle']
        },

        /**
         * Replace adding action URL for particular product types
         *
         * @return {String}
         */
        getAddToRequisitionListUrl: function () {
            var formObject = $(this.addToCartFormSelector);

            return this.excludedTypes.includes(this.productType) ?
                formObject.attr('action') :
                this._super();
        },

        /**
         * Skip form validation for current implementation
         *
         * @return {Boolean}
         */
        _isValidForm: function () {
            return true;
        }
    });
});
