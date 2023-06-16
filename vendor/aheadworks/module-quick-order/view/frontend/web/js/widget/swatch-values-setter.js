define([
    'jquery'
], function($) {
    "use strict";
    $.widget('awqo.swatchValuesSetter', {
        options: {
            classes: {
                optionClass: '.swatch-opt',
                attributeClass: 'swatch-attribute',
                selectClass: 'swatch-select'
            },
            values: ''
        },

        /**
         * Creates widget
         *
         * @private
         */
        _create: function () {
            this._setSwatchValues();
            $(this.options.classes.optionClass).on("swatch.initialized", this._setSwatchValues.bind(this));
        },

        /**
         * Set swatches
         *
         * @private
         */
        _setSwatchValues: function () {
            $.each(this.options.values, $.proxy(function (attributeId, optionId) {
                var elem = this.element.find('.' + this.options.classes.attributeClass +
                    '[attribute-id="' + attributeId + '"] [option-id="' + optionId + '"]' +
                    ', .' + this.options.classes.attributeClass +
                    '[data-attribute-id="' + attributeId + '"] [data-option-id="' + optionId + '"]'),
                    parentInput = elem.parent();

                if (elem.hasClass('selected')) {
                    return;
                }

                if (parentInput.hasClass(this.options.classes.selectClass)) {
                    parentInput.val(optionId);
                    parentInput.trigger('change');
                } else {
                    elem.trigger('click');
                }
            }, this));
        }
    });

    return $.awqo.swatchValuesSetter;
});
