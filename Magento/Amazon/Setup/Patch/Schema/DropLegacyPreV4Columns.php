<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Drop columns from old versions of the extension that aren't used anymore.
 */
class DropLegacyPreV4Columns implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    private $columnsToDrop = [
        ['table' => 'channel_amazon_account', 'column' => 'setup_step'],
        ['table' => 'channel_amazon_action', 'column' => 'api_group'],
        ['table' => 'channel_amazon_action', 'column' => 'api_action'],
        ['table' => 'channel_amazon_action', 'column' => 'api_content'],
    ];

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        foreach ($this->columnsToDrop as $columnData) {
            $table = $columnData['table'];
            $column = $columnData['column'];
            if ($connection->tableColumnExists($table, $column)) {
                $connection->dropColumn($table, $column);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            DropUnnecessaryIndexesLeftFromOldSchema::class
        ];
    }
}
