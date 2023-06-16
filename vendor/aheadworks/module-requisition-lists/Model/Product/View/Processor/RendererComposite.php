<?php
namespace Aheadworks\RequisitionLists\Model\Product\View\Processor;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template;
use Aheadworks\RequisitionLists\Model\Product\View\Processor\Renderer\RendererInterface;

/**
 * Class RendererComposite
 *
 * @package Aheadworks\RequisitionLists\Model\Product\View\Processor
 */
class RendererComposite
{
    /**
     * Base block
     */
    const BASE_BLOCK = 'product.info.options.wrapper';
    const BASE_BLOCK_TEMPLATE = 'Aheadworks_RequisitionLists::product/popup.phtml';

    /**
     * @var RendererInterface[]
     */
    private $rendererList = [];

    /**
     * @param array $rendererList
     */
    public function __construct(
        $rendererList = []
    ) {
        $this->rendererList = $rendererList;
    }

    /**
     * Render layout
     *
     * @param LayoutInterface $layout
     * @return string
     */
    public function render($layout)
    {
        $result = '';
        $block = $layout->getBlock(self::BASE_BLOCK);
        if ($block instanceof Template) {
            /** @var Template $block */
            $block->setTemplate(self::BASE_BLOCK_TEMPLATE);
            foreach ($this->rendererList as $renderer) {
                $renderer->render($layout, $block, $block->getProduct());
            }
            $result = $block->toHtml();
        }

        return $result;
    }
}
