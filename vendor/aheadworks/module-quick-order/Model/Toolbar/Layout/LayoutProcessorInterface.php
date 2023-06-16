<?php
namespace Aheadworks\QuickOrder\Model\Toolbar\Layout;

/**
 * Interface LayoutProcessorInterface
 *
 * @package Aheadworks\QuickOrder\Model\Toolbar\Layout
 */
interface LayoutProcessorInterface
{
    /**
     * Process js layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout);
}
