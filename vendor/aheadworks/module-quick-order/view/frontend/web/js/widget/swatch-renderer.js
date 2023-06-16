define([
    'jquery',
    'jquery/ui',
    'Magento_Swatches/js/swatch-renderer'
], function ($) {

    $.widget('awqo.SwatchRenderer', $.mage.SwatchRenderer, {

        /**
         * Update image without usage of media gallery from product page
         *
         * @param {Array} images
         * @param {jQuery} context
         * @param {Boolean} isInProductView
         */
        updateBaseImage: function (images, context, isInProductView) {
            this._super(images, context, false);
        }
    });

    return $.awqo.SwatchRenderer;
});