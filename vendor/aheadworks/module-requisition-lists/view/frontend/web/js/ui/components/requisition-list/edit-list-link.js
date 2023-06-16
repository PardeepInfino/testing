define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_RequisitionLists/components/requisition-list/edit-list-link',
            modules: {
                awRequisitionList: 'awRequisitionListParent.awRequisitionList'
            }
        },

        /**
         * Open modal action
         */
        openModal: function () {
            this.awRequisitionList().openModal();
        }
    });
});