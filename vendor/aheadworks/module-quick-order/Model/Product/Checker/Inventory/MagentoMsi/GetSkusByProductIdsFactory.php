<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi;

use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface;

/**
 * Class GetSkusByProductIdsFactory
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory\MagentoMsi
 */
class GetSkusByProductIdsFactory
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
     * @return GetSkusByProductIdsInterface
     */
    public function create()
    {
        return $this->objectManager->get(GetSkusByProductIdsInterface::class);
    }
}
