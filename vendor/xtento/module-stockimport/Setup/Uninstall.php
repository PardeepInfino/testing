<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-07-26T18:37:43+00:00
 * File:          Setup/Uninstall.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;


/**
 * Class Uninstall
 * @package Xtento\StockImport\Setup
 */
class Uninstall implements UninstallInterface
{
    public function __construct()
    {
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->dropTable($connection->getTableName('xtento_stockimport_log'));
        $connection->dropTable($connection->getTableName('xtento_stockimport_profile'));
        $connection->dropTable($connection->getTableName('xtento_stockimport_source'));

        $setup->endSetup();
    }
}
