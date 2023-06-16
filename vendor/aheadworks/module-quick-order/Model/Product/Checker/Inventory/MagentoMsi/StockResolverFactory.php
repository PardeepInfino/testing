<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

/**
 * Class StockResolverFactory
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi
 */
class StockResolverFactory
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
     * Get resolver instance
     *
     * @return StockResolverInterface
     */
    public function create()
    {
        return $this->objectManager->get(StockResolverInterface::class);
    }
}
