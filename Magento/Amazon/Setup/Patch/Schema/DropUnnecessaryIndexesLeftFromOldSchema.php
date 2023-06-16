<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * Deletes database indexes that are redundant or have duplicated due to migration to declarative schema.
 *
 * We had a lot of indexes that were not following Magento's naming convention.
 * Since the declarative schema cannot match them to indexes declared in the schema, it created duplicates for such cases.
 * We definitely do not want to have multiple indexes on a field, so we drop those that were not following convention.
 *
 * Also, during migration to declarative schema, we found that some indexes are redundant, and so we dropped them too.
 *
 * So if you'll compare pre-declarative schema indexes to the current state, reduced number of indexes is intentional.
 */
class DropUnnecessaryIndexesLeftFromOldSchema implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    private $indexesToDelete = [
        'channel_amazon_account' => [
            'CHANNEL_AMAZON_ACCOUNT_UNIQUEVALUE' => 'unique',
            'MERCHANT_ID_MERCHANT_ID' => 'index',
        ],
        'channel_amazon_account_listing' => [
            'CHANNEL_AMAZON_ACCOUNT_LISTING_UNIQUEVALUE' => 'unique',
            'MERCHNAT_ID_MERCHANT_ID' => 'index',
        ],
        'channel_amazon_account_order' => [
            'CHANNEL_AMAZON_ACCOUNT_ORDER_UNIQUEVALUE' => 'unique',
            'MERCHNAT_ID_MERCHANT_ID' => 'index',
        ],
        'channel_amazon_action' => [
            'CHANNEL_AMAZON_ACTION_UNIQUEVALUE' => 'unique',
            'CHANNEL_AMAZON_ACTION_UNIQUEVALUE_COMMAND' => 'unique',
        ],
        'channel_amazon_attribute' => [
            'AMAZON_ATTRIBUTE_AMAZON_ATTRIBUTE' => 'index',
            'CHANNEL_AMAZON_ATTRIBUTE_UNIQUEVALUE' => 'unique',
            'ID_ID' => 'index',
        ],
        'channel_amazon_attribute_value' => [
            'ASIN_ASIN' => 'index',
            'CHANNEL_AMAZON_ATTRIBUTE_VALUE_UNIQUEVALUE' => 'unique',
            'ID_ID' => 'index',
            'PARENT_ID_PARENT_ID' => 'index',
        ],
        'channel_amazon_defect' => [
            'CHANNEL_AMAZON_ACCOUNT_DEFECT_UNIQUEVALUE' => 'unique',
        ],
        'channel_amazon_error_log' => [
            'ID_ID' => 'index',
        ],
        'channel_amazon_listing' => [
            'ASIN_ASIN' => 'index',
            'CATALOG_PRODUCT_ID_CATALOG_PRODUCT_ID' => 'index',
            'CHANNEL_AMAZON_ACCOUNT_LISTING_UNIQUEVALUE' => 'unique',
            'CONDITION_CONDITION' => 'index',
            'IS_SHIP_IS_SHIP' => 'index',
            'LISTING_ID_LISTING_ID' => 'index',
            'LIST_STATUS_LIST_STATUS' => 'index',
            'MERCHANT_ID_MERCHANT_ID' => 'index',
        ],
        'channel_amazon_listing_log' => [
            'ID_ID' => 'index',
        ],
        'channel_amazon_listing_multiple' => [
            'CHANNEL_AMAZON_LISTING_MULTIPLE_UNIQUEVALUE' => 'unique',
        ],
        'channel_amazon_listing_rule' => [
            'MERCHANT_ID_MERCHANT_ID' => 'index',
        ],
        'channel_amazon_listing_variant' => [
            'CHANNEL_AMAZON_LISTING_VARIANT_UNIQUEVALUE' => 'unique',
        ],
        'channel_amazon_order' => [
            'CHANNEL_AMAZON_ORDER_UNIQUEVALUE' => 'unique',
            'ORDER_ID_ORDER_ID' => 'index',
        ],
        'channel_amazon_order_item' => [
            'CHANNEL_AMAZON_ORDER_ITEM_UNIQUEVALUE' => 'unique',
            'ORDER_ID_ORDER_ID' => 'index',
        ],
        'channel_amazon_order_reserve' => [
            'CHANNEL_AMAZON_ORDER_RESERVE_UNIQUEVALUE' => 'unique',
            'ORDER_ID_ORDER_ID' => 'index',
        ],
        'channel_amazon_order_tracking' => [
            'ORDER_ID_ORDER_ID' => 'index',
        ],
        'channel_amazon_pricing_bestbuybox' => [
            'ASIN_ASIN' => 'index',
            'CHANNEL_AMAZON_PRICING_BESTBUYBOX_UNIQUEVALUE' => 'unique',
            'COUNTRY_CODE_COUNTRY_CODE' => 'index',
        ],
        'channel_amazon_pricing_index' => [
            'ASIN_ASIN' => 'index',
            'CHANNEL_AMAZON_PRICING_INDEX_UNIQUEVALUE' => 'unique',
            'CONDITION_CONDITION' => 'index',
            'ID_ID' => 'index',
            'PARENT_ID_PARENT_ID' => 'index',
            'PRODUCT_ID_PRODUCT_ID' => 'index',
            'SHIPPING_CALCULATED_SHIPPING_CALCULATED' => 'index',
            'STOP_RULES_STOP_RULES' => 'index',
        ],
        'channel_amazon_pricing_lowest' => [
            'ASIN_ASIN' => 'index',
            'COUNTRY_CODE_COUNTRY_CODE' => 'index',
        ],
        'channel_amazon_pricing_rule' => [
            'ID_ID' => 'index',
        ],
        'channel_amazon_quantity_index' => [
            'CHANNEL_AMAZON_QUANTITY_INDEX_UNIQUEVALUE' => 'unique',
            'ID_ID' => 'index',
        ],
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
        foreach ($this->indexesToDelete as $table => $indexes) {
            foreach ($indexes as $index => $type) {
                $this->deleteIndex($table, $index, $type);
            }
        }
    }

    private function deleteIndex(string $table, string $indexName, string $indexType)
    {
        $connection = $this->moduleDataSetup->getConnection();
        $tableIndexes = $connection->getIndexList($table);
        $index = strtoupper($indexName);
        if (!isset($tableIndexes[$index])) {
            return;
        }
        if (strtoupper($tableIndexes[$index]['type']) !== strtoupper($indexType)) {
            throw new \RuntimeException(
                sprintf('Index %s does not match the type of %s on the table %s', $index, $indexType, $table)
            );
        }
        $connection->dropIndex($table, $indexName);
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
        return [];
    }
}
