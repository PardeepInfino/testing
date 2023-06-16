<?php
namespace Aheadworks\QuickOrder\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\QuickOrder\Model\ThirdPartyModule
 */
class Manager
{
    const MAGENTO_MSI_MODULE_NAME = 'Magento_InventorySalesApi';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Magento MSI module enabled
     *
     * @return bool
     */
    public function isMagentoMsiModuleEnabled()
    {
        return $this->moduleList->has(self::MAGENTO_MSI_MODULE_NAME);
    }
}
