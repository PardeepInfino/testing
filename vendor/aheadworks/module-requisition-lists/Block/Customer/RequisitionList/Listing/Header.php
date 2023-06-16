<?php
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Block\Customer\RequisitionList\Listing;

use Aheadworks\RequisitionLists\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Header
 * @package Aheadworks\RequisitionLists\Block\Customer\RequisitionList\Listing
 */
class Header extends Template
{
    /**
     * @var LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * @param Context $context
     * @param LayoutProcessorInterface[] $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
    }

    /**
     * Prepare JS layout of block
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            if (!$processor instanceof LayoutProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Layout processor does not implement required interface: %s.',
                        LayoutProcessorInterface::class
                    )
                );
            }
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return parent::getJsLayout();
    }
}
