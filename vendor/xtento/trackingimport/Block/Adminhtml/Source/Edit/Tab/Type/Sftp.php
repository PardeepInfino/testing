<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            %!uniqueid!%
 * Last Modified: 2016-03-11T17:40:19+00:00
 * File:          Block/Adminhtml/Source/Edit/Tab/Type/Sftp.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source\Edit\Tab\Type;

class Sftp extends Ftp
{
    // SFTP Configuration
    public function getFields(\Magento\Framework\Data\Form $form, $type = 'SFTP')
    {
        parent::getFields($form, $type);
    }
}