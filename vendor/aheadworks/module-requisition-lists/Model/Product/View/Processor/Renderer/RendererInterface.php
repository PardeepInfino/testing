<?php
namespace Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface RendererInterface
 *
 * @package Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer
 */
interface RendererInterface
{
    /**
     * Render layout
     *
     * @param LayoutInterface $layout
     * @param Template $block
     * @param ProductInterface $product
     * @return $this
     */
    public function render($layout, $block, $product);
}
