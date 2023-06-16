define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return {

        /**
         * Get configure item url
         *
         * @return {String}
         */
        getConfigureItemUrl: function () {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().configureItemUrl;
            }

            return url;
        },

        /**
         * Get update item option url
         *
         * @return {String}
         */
        getUpdateItemOptionUrl: function () {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().updateItemOptionUrl;
            }

            return url;
        },

        /**
         * Get remove item url
         *
         * @return {String}
         */
        getRemoveItemUrl: function (item) {
            var url = '';

            if (this._getConfig()) {
                url = this._getConfig().removeItemUrl + 'item_id/' + item.item_id;
            }

            return url;
        },

        /**
         * Get config
         *
         * @return {*}
         * @private
         */
        _getConfig: function () {
            return registry.get('aw_requisition_list_config');
        }
    }
});
