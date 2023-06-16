<?php
namespace Aheadworks\QuickOrder\Block\Product\Renderer\Swatches;

use Magento\Swatches\Block\Product\Renderer\Configurable as SwatchesConfigurable;

/**
 * Class Configurable
 *
 * @package Aheadworks\QuickOrder\Block\Product\Renderer\Swatches
 */
class Configurable extends SwatchesConfigurable
{
    /**
     * Swatch template
     */
    const TEMPLATE = 'Aheadworks_QuickOrder::product/renderer/swatch/renderer.phtml';

    /**
     * Get renderer template
     *
     * @return string
     */
    protected function getRendererTemplate()
    {
        return self::TEMPLATE;
    }

    /**
     * @inheritdoc
     */
    public function getCacheLifetime()
    {
        return 0;
    }

    /**
     * Get selected swatches values as JSON
     *
     * @return string
     */
    public function getSelectedSwatchesJson()
    {
        $product = $this->getProduct();
        $preconfiguredValues = $product->getPreconfiguredValues();
        $options = $preconfiguredValues ? $preconfiguredValues->getSuperAttribute() : [];
        return \Zend_Json::encode($options);
    }
}
