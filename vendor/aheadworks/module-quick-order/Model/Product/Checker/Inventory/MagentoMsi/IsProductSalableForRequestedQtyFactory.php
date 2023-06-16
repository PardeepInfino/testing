<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;

/**
 * Class IsProductSalableForRequestedQtyFactory
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi
 */
class IsProductSalableForRequestedQtyFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Get instance
     *
     * @return IsProductSalableForRequestedQtyInterface
     */
    public function create()
    {
        return $this->objectManager->get(IsProductSalableForRequestedQtyInterface::class);
    }
}
