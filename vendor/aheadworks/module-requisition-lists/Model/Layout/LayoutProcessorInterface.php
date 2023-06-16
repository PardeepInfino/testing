<?php
namespace Aheadworks\RequisitionLists\Model\Layout;

/**
 * Interface LayoutProcessorInterface
 *
 * @package Aheadworks\RequisitionLists\Model\Toolbar\Layout
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
