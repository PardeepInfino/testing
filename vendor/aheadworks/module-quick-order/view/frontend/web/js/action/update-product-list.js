define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return function () {
        registry.async('aw_quick_order_item_listing.aw_quick_order_item_listing_data_source')(
            function (dataSource) {
                dataSource.reload();
            }.bind(this)
        );
    }
});
