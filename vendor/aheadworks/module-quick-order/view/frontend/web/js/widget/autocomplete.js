define([
    'jquery',
    'mage/template',
    'mage/translate',
    'Aheadworks_QuickOrder/js/action/send-request',
    'jquery-ui-modules/autocomplete'
], function ($, mageTemplate, $t, sendRequest) {
    'use strict';

    $.widget('mage.awQoAutocomplete', $.ui.autocomplete, {
        options: {
            minLength: 3,
            delay: 100,
            searchUrl: '',
            noProductFound: $t('No Product Found'),
            source: function (request, response) {
                this._prepareSource(request, response);
            }
        },
        request: null,

        /**
         * Prepare source
         *
         * @param {Object} request
         * @param {Function} response
         * @private
         */
        _prepareSource: function(request, response) {
            if (this.request) {
                this.request.abort();
            }

            this.element.addClass('loading');
            this.request = sendRequest(this.options.searchUrl, {"q": request.term});
            this.request
                .done(function(result) {
                    if (result.length > 0) {
                        response(result);
                    } else {
                        response([{
                            notFoundMessage: this.options.noProductFound,
                        }]);
                    }
                }.bind(this))
                .always(function () {
                    this.element.removeClass('loading');
                }.bind(this));
        },

        /**
         * @inheritdoc
         */
        _create: function () {
            this._super();
            this._keyEventEnter();
        },

        /**
         * @inheritdoc
         */
        _renderItem: function (ul, item) {
            var html = mageTemplate("#aw-qo-search-result-item", {'item': item});

            return $(html).appendTo(ul);
        },

        /**
         * Event Enter Press
         *
         * @private
         */
        _keyEventEnter: function() {
            var self = this,
                keyCode = $.ui.keyCode;

            $(this.element).on('keydown', function (event) {
                if (event.keyCode == keyCode.ENTER || event.keyCode == keyCode.NUMPAD_ENTER) {
                    self.close(event);
                }
            });
        }
    });

    return $.mage.awQoAutocomplete
});
