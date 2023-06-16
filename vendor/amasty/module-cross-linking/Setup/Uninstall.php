<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    private const CROSS_LINK_TABLE = 'amasty_cross_link';
    private const CROSS_LINK_STORE_TABLE = 'amasty_cross_link_store';

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $this->uninstallTables($setup);
        $this->uninstallConfigData($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): void
    {
        $tablesToDrop = [
            self::CROSS_LINK_TABLE,
            self::CROSS_LINK_STORE_TABLE
        ];

        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): void
    {
        $configTable = $setup->getTable('core_config_data');
        $setup->getConnection()->delete($configTable, "`path` LIKE 'amasty_cross_links%'");
    }
}
