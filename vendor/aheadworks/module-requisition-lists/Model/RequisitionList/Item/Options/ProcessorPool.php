<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options;

use Magento\Catalog\Model\ProductOptionProcessorInterface;

/**
 * Class ProcessorPool
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options
 */
class ProcessorPool
{
    /**
     * @var ProductOptionProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Retrieve product option processor
     *
     * @param string $type
     * @return ProductOptionProcessorInterface|null
     */
    public function get($type)
    {
        $processor = null;
        if (isset($this->processors[$type])) {
            $processor = $this->processors[$type];
        }

        return $processor;
    }
}
