define([
    'jquery',
    'underscore',
], function ($, _) {
    'use strict';

    return function (widget) {
        $.widget('mage.multipleWishlist', widget, {
            options: {
                splitBtnWishlist: '.split.button.wishlist',
                giftCartOptionsItem: '.gift-options-cart-item'
            },

            /**
             * Move gift cart options
             */
            _buildWishlistDropdown: function () {
                this._super();

                if (this.options.wishlists && this.options.wishlists.length > 0) {
                    $(this.options.splitBtnWishlist).each($.proxy(function (index, e) {
                        var element = $(e),
                            optionsItem = element.siblings(this.options.giftCartOptionsItem);

                        if (optionsItem.length) {
                            optionsItem.prependTo(optionsItem.parent());
                        }
                    }, this));
                }
            }
        });

        return $.mage.multipleWishlist;
    }
});
