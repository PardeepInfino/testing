define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_RequisitionLists/components/requisition-list/create-list-link',
            modules: {
                requisitionListModalForm: 'aw_requisition_list_modal_form.aw_requisition_list_modal_form'
            }
        },

        /**
         * Open modal action
         */
        openModal: function () {
            this.requisitionListModalForm().openModal();
        }
    });
});
