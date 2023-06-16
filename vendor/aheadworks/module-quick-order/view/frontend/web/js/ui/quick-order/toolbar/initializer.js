define([
    'uiElement',
    'jquery'
], function (Element, $) {
    'use strict';

    return Element.extend({
        defaults: {
            imports: {
                toolbarId: '${ $.parentName }:toolbarId'
            }
        },

        /**
         * @inheritdoc
         *
         * mage.tabs is loaded after ui.tabs and takes precedence,
         * however mage.tabs doesn't work at all for some reason and
         * we need to initialize tabs by ui.tabs widget.
         */
        initialize: function () {
            this._super();
            $.widget.bridge("uiTabs", $.ui.tabs);

            return this;
        },

        /**
         * Tabs are getting initialized once this last child is loaded
         */
        onToolbarRender: function () {
            window.FORM_KEY = $.mage.cookies.get('form_key');
            $(function() {
                $("#" + this.toolbarId).uiTabs();
            }.bind(this));
        }
    });
});