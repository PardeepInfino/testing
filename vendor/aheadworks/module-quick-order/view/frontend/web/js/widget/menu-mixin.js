define([
    'jquery',
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.menu', widget.menu, {
            _create: function () {
                $(this.element).data('ui-menu', this);
                this._super();
            }
        });

        return $.mage.menu;
    };
});