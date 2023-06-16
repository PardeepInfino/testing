define([
    'jquery',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function ($, utils, $t, alert) {
    "use strict";

    return function (deferred, data) {
        $.ajax({
            url: data.url,
            type: "POST",
            data: utils.serialize(data.params),
            dataType: 'json',

            /**
             * Success callback
             *
             * @param {Object} response
             * @returns {Boolean}
             */
            success: function (response) {
                deferred.resolve();
            },

            /**
             * Error callback
             *
             * @param {Object} response
             * @returns {Boolean}
             */
            error: function (response) {
                alert({
                    title: $t('There has been an error'),
                    content: response.statusText,
                });
                deferred.reject();
            }
        });
    }
});
