define([
    'jquery',
    'uiElement',
], function ($, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            systemMessageSelector: '.page div[role=alert]'
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe(
                    {
                        'messageList': []
                    });

            return this;
        },

        /**
         * Render messages
         *
         * @param {Object} messageData
         */
        render: function(messageData) {
            this.hideSystemMessages();
            this.messageList(messageData);
        },

        /**
         * Hide system messages
         */
        hideSystemMessages: function () {
            $(this.systemMessageSelector).hide();
        }
    });
});
