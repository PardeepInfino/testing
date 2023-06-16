define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return {

        /**
         * Show messages
         *
         * @param {Object} messageData
         */
        showMessages: function(messageData) {
            registry.async('aw_quick_order_messages')(
                function (messages) {
                    messages.render(messageData);
                }.bind(this)
            );
        }
    }
});
