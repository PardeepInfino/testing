<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Setup\Patch\DeclarativeSchemaApplyBefore;

use Magento\Framework\DB\Select;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class DeleteExtraStoreIds implements DataPatchInterface
{
    private const CROSS_LINK_TABLE = 'amasty_cross_link';
    private const CROSS_LINK_STORE_TABLE = 'amasty_cross_link_store';
    private const CROSS_LINK_ID_COLUMN = 'link_id';
    private const STORE_ID_COLUMN = 'store_id';
    private const STORE_TABLE = 'store';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $crossLinkStoreTable = $this->moduleDataSetup->getTable(self::CROSS_LINK_STORE_TABLE);

        if ($this->moduleDataSetup->tableExists($crossLinkStoreTable)) {
            $connection = $this->moduleDataSetup->getConnection();
            $linkIdsSelect = $this->getLinkIdsSelect($connection);
            $storeIdsSelect = $this->getStoreIdsSelect($connection);
            $condition = sprintf(
                '%s NOT IN (%s) OR %s NOT IN (%s)',
                self::CROSS_LINK_ID_COLUMN,
                $linkIdsSelect->assemble(),
                self::STORE_ID_COLUMN,
                $storeIdsSelect->assemble()
            );
            $extraLinksSelect = $connection->select()
                ->from($crossLinkStoreTable)
                ->where($condition);
            $connection->query($extraLinksSelect->deleteFromSelect($crossLinkStoreTable));
        }

        return $this;
    }

    public function getLinkIdsSelect(AdapterInterface $connection): Select
    {
        $crossLinkTable = $this->moduleDataSetup->getTable(self::CROSS_LINK_TABLE);

        return $connection->select()->from($crossLinkTable, self::CROSS_LINK_ID_COLUMN);
    }

    public function getStoreIdsSelect(AdapterInterface $connection): Select
    {
        $storeTable = $this->moduleDataSetup->getTable(self::STORE_TABLE);

        return $connection->select()->from($storeTable, self::STORE_ID_COLUMN);
    }
}
