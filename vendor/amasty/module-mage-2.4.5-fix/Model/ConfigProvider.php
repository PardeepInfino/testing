<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Mage 2.4.5 fix by Amasty for Magento 2 (System)
 */

namespace Amasty\Mage245Fix\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    public const IS_MAGENTO_CHANGES_DISABLED_IN_ILN = 'amshopby/developer/is_magento_changes_disabled';
    public const IS_MAGENTO_CHANGES_DISABLED_IN_SORTING = 'amsorting/developer/is_magento_changes_disabled';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isMagentoChangesDisabled(?int $storeId = null): bool
    {
        return $this->isMagentoChangesDisabledInIln() && $this->isMagentoChangesDisabledInSorting();
    }

    public function isMagentoChangesDisabledInIln(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::IS_MAGENTO_CHANGES_DISABLED_IN_ILN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isMagentoChangesDisabledInSorting(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::IS_MAGENTO_CHANGES_DISABLED_IN_SORTING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
