<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-10-08T16:46:41+00:00
 * File:          Block/Adminhtml/Log/Grid/Renderer/ResultMessage.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml\Log\Grid\Renderer;

class ResultMessage extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        return nl2br(parent::_getValue($row));
    }
}
