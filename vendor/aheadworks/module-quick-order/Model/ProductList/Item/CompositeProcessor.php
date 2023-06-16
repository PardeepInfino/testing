<?php
namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\Exception\OperationException;
use Aheadworks\QuickOrder\Model\Product\AvailabilityChecker;

/**
 * Class CompositeProcessor
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item
 */
class CompositeProcessor
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @param AvailabilityChecker $availabilityChecker
     * @param array $processors
     */
    public function __construct(
        AvailabilityChecker $availabilityChecker,
        $processors = []
    ) {
        $this->availabilityChecker = $availabilityChecker;
        $this->processors = $processors;
    }

    /**
     * Prepare product list item
     *
     * @param ItemDataInterface $requestItem
     * @param ProductListItemInterface $productListItem
     * @param ProductInterface $product
     * @return ProductListItemInterface
     * @throws OperationException
     */
    public function process($requestItem, $productListItem, $product)
    {
        if (!$this->availabilityChecker->isAvailable($product, $productListItem)) {
            throw new OperationException(__('The product is not available'));
        }

        foreach ($this->processors as $builder) {
            if (!$builder instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Item builder does not implement required interface: %s.',
                        ProcessorInterface::class
                    )
                );
            }

            $builder->process($requestItem, $productListItem, $product);
        }

        return $productListItem;
    }
}
