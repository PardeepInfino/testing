define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/tree-massactions',
    'ko'
], function ($, _, TreeMassActions, ko) {
    'use strict';

    return TreeMassActions.extend({
        defaults: {
            imports: {
                visible: '${ $.provider }:data.totalRecords'
            },
            tracks: {
                visible: true
            },
            modules: {
                requisitionListModalForm: 'aw_requisition_list_modal_form.aw_requisition_list_modal_form'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._addFormKeyIfNotSet();

            return this._super();
        },

        /**
         * @inheritdoc
         */
        recursiveObserveActions: function (actions, prefix) {
            _.each(actions, function (action) {
                if (prefix) {
                    action.type = prefix + '.' + action.type;
                }

                if (action.actions) {
                    action.observableActions = ko.observableArray(action.actions.slice(0, action.resultLimit || this.resultLimit));
                    action.visible = ko.observable(false);
                    action.parent = actions;
                    this.recursiveObserveActions(action.actions, action.type);
                }
            }, this);

            return this;
        },

        /**
         * Applies specified action.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @returns {TreeMassActions} Chainable.
         */
        applyAction: function (actionIndex) {
            var action = this.getAction(actionIndex);

            if (action.click) {
                eval(action.click);
                return this;
            }

            return this._super(actionIndex);
        },

        /**
         * Add form key to window object if form key is not added earlier
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
        }
    });
});
