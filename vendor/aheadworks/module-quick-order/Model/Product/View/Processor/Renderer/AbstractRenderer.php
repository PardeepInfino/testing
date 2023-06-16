<?php
namespace Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer;

use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class AbstractRenderer
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @inheritdoc
     */
    abstract public function render($layout, $block, $product);

    /**
     * Append block
     *
     * @param BlockInterface|AbstractBlock $parentBlock
     * @param BlockInterface|AbstractBlock $childBlock
     * @param string $alias
     */
    protected function appendBlock($parentBlock, $childBlock, $alias)
    {
        $parentBlock->append($childBlock, $alias);
        $this->addOptionChildBlockName($parentBlock, $alias);
    }

    /**
     * Add option child block name
     *
     * @param BlockInterface|AbstractBlock $block
     * @param string $name
     */
    protected function addOptionChildBlockName($block, $name)
    {
        $optionBlockNames = $block->getOptionBlockNames() ?? [];
        $optionBlockNames[] = $name;
        $block->setOptionBlockNames($optionBlockNames);
    }
}
