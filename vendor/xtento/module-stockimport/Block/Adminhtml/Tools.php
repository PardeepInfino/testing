<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Block/Adminhtml/Tools.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml;

class Tools extends \Magento\Backend\Block\Template
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Xtento_StockImport::tools.phtml');
    }
}
