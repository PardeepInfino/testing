<?php
namespace Aheadworks\RequisitionLists\Model\Product\Checker\Inventory;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\RequisitionLists\Model\ThirdPartyModule\Manager;

/**
 * Class IsSalableResultFactory
 *
 * @package Aheadworks\RequisitionLists\Model\Product\Checker\Inventory
 */
class IsSalableResultFactory
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Manager $moduleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Manager $moduleManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * Create is product salable result
     *
     * @return IsProductSalableResultInterface
     */
    public function create()
    {
        return $this->moduleManager->isMagentoMsiModuleEnabled()
            ? $this->objectManager->get(MagentoMsiIsSalableResult::class)
            : $this->objectManager->get(InventoryIsSalableResult::class);
    }
}
