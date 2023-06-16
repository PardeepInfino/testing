<?php
namespace Aheadworks\QuickOrder\Model\Product\Option;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;

/**
 * Class ProcessorPool
 *
 * @package Aheadworks\QuickOrder\Model\Product\Option
 */
class ProcessorPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductOptionProcessorInterface[]
     */
    private $processors;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $processors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $processors = []
    ) {
        $this->objectManager = $objectManager;
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
        if (isset($this->processors[$type]) && class_exists($this->processors[$type])) {
            $processor = $this->objectManager->get($this->processors[$type]);
        }

        return $processor;
    }
}
