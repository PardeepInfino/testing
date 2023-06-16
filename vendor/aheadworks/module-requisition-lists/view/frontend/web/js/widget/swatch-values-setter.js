define([
    'jquery'
], function($) {
    "use strict";
    $.widget('awrl.swatchValuesSetter', {
        options: {
            classes: {
                configureItemForm: '#aw-rl-configure-item-form',
                attributeSelectedOptionLabelClass: 'swatch-attribute-selected-option',
                attributeInput: 'swatch-input',
                optionClass: '.swatch-opt'
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
            var parent, label, input, selectedOption, attributeId, attributeValue,
                self = this,
                values = this.options.values,
                attributePrefix = this.options.attributePrefix;

            for (attributeId in values) {
                attributeValue = values[attributeId];
                if (!attributeValue) {
                    return true;
                }
                parent = this.element.find('[' + attributePrefix + 'attribute-id="' + attributeId + '"]');
                label = parent.find('.' + self.options.classes.attributeSelectedOptionLabelClass);
                selectedOption = parent.find('[' + attributePrefix + 'option-id="' + attributeValue + '"]');
                input = $(
                    self.options.classes.configureItemForm +
                    ' .' + self.options.classes.attributeInput +
                    '[name="super_attribute[' + attributeId + ']"]'
                );

                label.text(selectedOption.attr(attributePrefix + 'option-label'));
                input.val(attributeValue);
                selectedOption.addClass('selected');
            }
        }
    });

    return $.awrl.swatchValuesSetter;
});
