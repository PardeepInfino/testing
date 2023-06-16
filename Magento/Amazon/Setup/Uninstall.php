<?php

namespace Magento\Amazon\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Tables to delete when during the module uninstall
     *
     * ORDER MATTERS because of foreign keys.
     *
     * @var string[]
     */
    private $tables = [
        'channel_amazon_account_listing',
        'channel_amazon_account_order',
        'channel_amazon_action',
        'channel_amazon_attribute_value',
        'channel_amazon_attribute',
        'channel_amazon_defect',
        'channel_amazon_error_log',
        'channel_amazon_listing_log',
        'channel_amazon_listing_multiple',
        'channel_amazon_listing_rule',
        'channel_amazon_listing_variant',
        'channel_amazon_listing',
        'channel_amazon_log_processing',
        'channel_amazon_logs',
        'channel_amazon_order_item',
        'channel_amazon_order_reserve',
        'channel_amazon_order_tracking',
        'channel_amazon_order',
        'channel_amazon_pricing_bestbuybox',
        'channel_amazon_pricing_index',
        'channel_amazon_pricing_lowest',
        'channel_amazon_pricing_rule',
        'channel_amazon_quantity_index',
        'channel_amazon_sync_status',
        'channel_amazon_account',
    ];

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();

        foreach ($this->tables as $table) {
            if ($connection->isTableExists($table)) {
                $connection->dropTable($table);
            }
        }
    }
}
