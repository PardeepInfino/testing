<?php
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Block\Product\Renderer\Swatches;

use Aheadworks\RequisitionLists\ViewModel\Catalog\Product\Renderer\Swatches\ConfigurableProvider;
use Magento\Swatches\Block\Product\Renderer\Configurable as SwatchesConfigurable;

/**
 * Class Configurable
 * @package Aheadworks\RequisitionLists\Block\Product\Renderer\Swatches
 */
class Configurable extends SwatchesConfigurable
{
    /**
     * Swatch template
     */
    const TEMPLATE = 'Aheadworks_RequisitionLists::product/renderer/swatch/renderer.phtml';

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
    public function getSelectedSwatchesJson(): string
    {
        $preconfiguredValues = $this->getProduct()->getPreconfiguredValues();
        $options = $preconfiguredValues ? $preconfiguredValues->getSuperAttribute() : [];

        return $this->jsonEncoder->encode($options);
    }

    /**
     * Get view model
     *
     * @return ConfigurableProvider
     */
    public function getViewModel()
    {
        return $this->_data['viewModel'];
    }
}
