<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2020-07-26T18:38:06+00:00
 * File:          Setup/Uninstall.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;


/**
 * Class Uninstall
 * @package Xtento\TrackingImport\Setup
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
        $connection->dropTable($connection->getTableName('xtento_trackingimport_log'));
        $connection->dropTable($connection->getTableName('xtento_trackingimport_profile'));
        $connection->dropTable($connection->getTableName('xtento_trackingimport_source'));

        $setup->endSetup();
    }
}
