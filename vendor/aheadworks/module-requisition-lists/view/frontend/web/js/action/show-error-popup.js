define([
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function ($t, alert) {
    "use strict";

    return function (title, content) {
        alert({
            title: $t(title),
            content: content,
        });
    }
});
