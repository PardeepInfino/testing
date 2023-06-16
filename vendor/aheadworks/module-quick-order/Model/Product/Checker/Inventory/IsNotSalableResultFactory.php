<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\QuickOrder\Model\ThirdPartyModule\Manager;

/**
 * Class IsNotSalableResultFactory
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
class IsNotSalableResultFactory
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
     * @return IsNotSalableForRequestedQtyResultInterface
     */
    public function create()
    {
        return $this->moduleManager->isMagentoMsiModuleEnabled()
            ? $this->objectManager->get(MagentoMsiIsNotSalableResult::class)
            : $this->objectManager->get(InventoryIsNotSalableResult::class);
    }
}
