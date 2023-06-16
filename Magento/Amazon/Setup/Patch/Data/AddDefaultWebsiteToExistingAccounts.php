<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Looks for migrated accounts that were created, but a user didn't complete setup step before upgrade
 * and so an account does not have assigned website yet.
 * If such account exists, we assign them a default website or first website in the list if there's no default website.
 *
 * If a user isn't happy with the selection, they could delete the account and re-create it with a proper configuration.
 */
class AddDefaultWebsiteToExistingAccounts implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup, StoreManagerInterface $storeManager)
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeManager = $storeManager;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $accountsTable = $this->moduleDataSetup->getTable('channel_amazon_account');
        $listingRuleTable = $this->moduleDataSetup->getTable('channel_amazon_listing_rule');
        $accountsWithoutWebsitesSelect = $connection->select()
            ->from(['account' => $accountsTable], ['merchant_id'])
            ->joinLeft(['rule' => $listingRuleTable], 'account.merchant_id = rule.merchant_id', [])
            ->where('rule.website_id IS NULL');
        $merchantIds = $connection->fetchCol($accountsWithoutWebsitesSelect);
        if (!$merchantIds) {
            return;
        }

        $websites = $this->storeManager->getWebsites();
        $defaultWebsite = null;
        foreach ($websites as $website) {
            $isDefault = $website->getData('is_default');
            if (null === $defaultWebsite || $isDefault) {
                $defaultWebsite = $website;
            }
        }
        if (!$defaultWebsite) {
            return;
        }

        $data = [];
        foreach ($merchantIds as $merchantId) {
            $data[] = [$merchantId, '{}', $defaultWebsite->getId()];
        }
        $connection->insertArray(
            $listingRuleTable,
            ['merchant_id', 'conditions_serialized', 'website_id'],
            $data
        );
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
