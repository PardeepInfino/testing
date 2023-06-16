<?php
namespace Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer;

use Aheadworks\QuickOrder\Block\Product\Renderer\Image as ProductImage;

/**
 * Class Image
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer
 */
class Image implements RendererInterface
{
    /**
     * @inheritdoc
     */
    public function render($layout, $block, $product)
    {
        $imageBlock = $layout->createBlock(
            ProductImage::class,
            'aw_qo.popup.product-image',
            ['data' => ['product' => $product]]
        );
        $block->append($imageBlock, 'product_image');

        return $this;
    }
}
