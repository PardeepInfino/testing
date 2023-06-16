<?php
namespace Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer;

use Aheadworks\RequisitionLists\ViewModel\Catalog\Product\Renderer\Swatches\ConfigurableProvider;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;
use Aheadworks\RequisitionLists\Block\Product\Renderer\Swatches\Configurable;

/**
 * Class Options
 * @package Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer
 */
class Options implements RendererInterface
{
    /**
     * #@+
     * Block names with options for different products
     */
    const CONFIGURABLE_OPTIONS_BLOCK = 'product.info.options.configurable';
    const SWATCH_OPTIONS_BLOCK = 'product.info.options.swatches';
    const GROUPED_OPTIONS_BLOCK = 'product.info.grouped';
    const BUNDLE_OPTIONS_BLOCK = 'product.info.bundle.options';
    const DOWNLOADABLE_OPTIONS_BLOCK = 'product.info.downloadable.options';
    /**#@-*/

    /**
     * @var ConfigurableProvider
     */
    private $viewModel;

    /**
     * @param ConfigurableProvider $viewModel
     */
    public function __construct(
        ConfigurableProvider $viewModel
    ) {
        $this->viewModel = $viewModel;
    }

    /**
     * @inheritdoc
     */
    public function render($layout, $block, $product)
    {
        $this->appendConfigurable($layout, $block)
            ->appendSwatches($layout, $block)
            ->appendGrouped($layout, $block)
            ->appendBundle($layout, $block)
            ->appendDownloadable($layout, $block);

        return $this;
    }

    /**
     * Append configurable info block
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @return $this
     */
    private function appendConfigurable($layout, $block)
    {
        $configurableBlock = $layout->getBlock(self::CONFIGURABLE_OPTIONS_BLOCK);
        if ($configurableBlock instanceof Template) {
            /** @var Template $configurableBlock */
            $block->append($configurableBlock, 'product_options_configurable');
        }

        return $this;
    }

    /**
     * Append swatches block
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @return $this
     */
    private function appendSwatches($layout, $block)
    {
        $swatchesBlock = $layout->getBlock(self::SWATCH_OPTIONS_BLOCK);
        if ($swatchesBlock instanceof Template) {
            $swatchesBlock = $layout->createBlock(
                Configurable::class,
                'aw_rl.popup.options_configurable',
                ['data' => [
                    ['product' => $block->getProduct()],
                    'viewModel' => $this->viewModel]
                ]
            );
            $block->append($swatchesBlock, 'product_options_configurable');
        }

        return $this;
    }

    /**
     * Append grouped info block
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @return $this
     */
    private function appendGrouped($layout, $block)
    {
        $groupedBlock = $layout->getBlock(self::GROUPED_OPTIONS_BLOCK);
        if ($groupedBlock instanceof Template) {
            /** @var Template $groupedBlock */
            $block->unsetChild('product_qty');
            $block->unsetChild('product_price');
            $block->append($groupedBlock, 'product_options_grouped');
        }

        return $this;
    }

    /**
     * Append bundle info block
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @return $this
     */
    private function appendBundle($layout, $block)
    {
        $bundleBlock = $layout->getBlock(self::BUNDLE_OPTIONS_BLOCK);
        if ($bundleBlock instanceof Template) {
            /** @var Template $bundleBlock */
            $bundleBlock->setTemplate('Aheadworks_RequisitionLists::product/renderer/bundle/renderer.phtml');
            $block->append($bundleBlock, 'product_options_bundle');
        }

        return $this;
    }

    /**
     * Append downloadable info block
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @return $this
     */
    private function appendDownloadable($layout, $block)
    {
        $downloadableBlock = $layout->getBlock(self::DOWNLOADABLE_OPTIONS_BLOCK);
        if ($downloadableBlock instanceof Template) {
            /** @var Template $groupedBlock */
            $block->append($downloadableBlock, 'product_options_downloadable');
        }

        return $this;
    }
}
