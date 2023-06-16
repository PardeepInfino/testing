<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2016-03-13T19:15:44+00:00
 * File:          Model/ResourceModel/Profile.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\ResourceModel;

class Profile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_serializableFields = [
        'configuration' => [null, []]
    ];

    protected function _construct()
    {
        $this->_init('xtento_trackingimport_profile', 'profile_id');
    }
}
