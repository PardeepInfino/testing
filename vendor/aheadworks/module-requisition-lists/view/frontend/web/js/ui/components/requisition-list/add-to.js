define([
    'ko',
    'underscore',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (ko, _, $, Component, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            name: 'addToProductLink',
            template: 'Aheadworks_RequisitionLists/components/requisition-list/add-to',
            sectionName: 'requisitionlist',
            addToRequisitionListUrl: '',
            urlToRequisitionList: '',
            addToLinkSelector: '.addto-requisition-list',
            addToCartFormSelector: '#product_addtocart_form',
            buttonToggleSelector: '.aw-rl__list-wrapper .toggle.change',
            isLoggined: false,
            linkLabel: 'Add to Requisition List',
            modules: {
                requisitionListModalForm: 'aw_requisition_list_modal_form.aw_requisition_list_modal_form'
            }
        },

        /**
         * Get customer Lists
         */
        getLists: function () {
            return customerData.get(this.sectionName)()['lists'];
        },

        /**
         * Trigger event after render element
         */
        onElementRender: function () {
            $(this.buttonToggleSelector).trigger('contentUpdated');
        },

        /**
         * Get Url to requisition list page
         *
         * @returns {string}
         */
        getUrlToRequisitionPage: function () {
            return this.urlToRequisitionList;
        },

        /**
         * Button action
         */
        applyAction: function(listData) {
            this.listId = listData.list_id;

            if (!_.isEmpty(this.addToCartFormSelector)) {
                this._submitForm();
            }
        },

        /**
         * Retrieve action URL for adding to requisition list
         *
         * @returns {String}
         */
        getAddToRequisitionListUrl: function () {
            var formObject = $(this.addToCartFormSelector);

            return _.isEmpty(this.addToRequisitionListUrl) ?
                formObject.attr('action') :
                this.addToRequisitionListUrl;
        },

        /**
         * Submit form
         *
         * @returns void
         */
        _submitForm: function () {
            var formObject = $(this.addToCartFormSelector),
                formData;

            if (formObject.length) {
                if (this._isValidForm()) {
                    formData = new FormData(formObject[0]);
                    formData.set('list_id', this.listId);

                    $.post({
                        url: this.getAddToRequisitionListUrl(),
                        data: formData,
                        showLoader: true,
                        contentType: false,
                        processData: false,

                        success: function (data) {
                            if (data.backUrl) {
                                this.redirect(data.backUrl);
                            }
                        }.bind(this)
                    });
                }
            } else {
                this.redirect(this.addToRequisitionListUrl);
            }
        },

        /**
         * Check if form valid
         *
         * @return {Boolean}
         */
        _isValidForm: function() {
            if (_.isEmpty(this.addToCartFormSelector)) {
                return true;
            }

            var event = $.Event('additional.validation'),
                formObject = $(this.addToCartFormSelector),
                isValid;

            // Validate UI component from form
            formObject.trigger(event);
            isValid = formObject.valid();

            return event.isDefaultPrevented() == false && isValid;
        },

        /**
         * Open modal action
         */
        openModal: function () {
            if (this.isLoggined) {
                this.requisitionListModalForm().openModal();
            } else {
                this.redirect(this.getUrlToRequisitionPage());
            }
        },

        /**
         * Redirect to page
         */
        redirect: function (url) {
            window.location = url;
        }
    });
});
