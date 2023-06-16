define([
    'jquery',
    'mageUtils'
], function ($, utils) {
    "use strict";

    return function (url, params) {
        var setup = {
            url: url,
            type: "POST",
            dataType: 'json'
        };
        if (params instanceof FormData) {
            setup.processData = false;
            setup.contentType = false;
            setup.data = params;
        } else {
            setup.data = utils.serialize(params);
        }

        return $.ajax(setup);
    }
});
