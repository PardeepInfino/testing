<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2019-02-05T17:10:52+00:00
 * File:          Block/Adminhtml/Profile/Edit/Tab/Mapping/Defaultvalues.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Block\Adminhtml\Profile\Edit\Tab\Mapping;

use Magento\Framework\View\Element\AbstractBlock;

class Defaultvalues extends AbstractBlock
{
    public function _toHtml()
    {
        $column = $this->getColumn();

        $html = '<input type="text" id="' . $this->getInputName() . '" name="' . $this->getInputName(
            ) . '" value="#{' . $this->getColumnName() . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';

        // Is it a select or a text field?
        $html .= <<<JS
        <script>
            var default_values = {$this->getMappingId()}_possible_default_values.get($(\'select_#{_id}\').value);
            if (default_values) {
                var field = $(\'{$this->getMappingId()}[#{_id}][default_value]\').parentNode;
                field.innerHTML = \'\';
                select = document.createElement(\'select\');
                select.setAttribute(\'style\', \'width: 98%;\');
                select.setAttribute(\'id\', \'select_default_#{_id}\');
                select.setAttribute(\'name\', \'{$this->getInputName()}\');
                select.setAttribute(\'class\', \'select\');
                option = document.createElement(\'option\');
                optionText = document.createTextNode(\'--- Select value ---\');
                option.appendChild(optionText);
                option.setAttribute(\'value\', \'\');
                select.appendChild(option);
                \$H(default_values).each(function(pair) {
                    option = document.createElement(\'option\');
                    optionText = document.createTextNode(pair.value);
                    option.appendChild(optionText);
                    option.setAttribute(\'value\', pair.key);
                    select.appendChild(option);
                });
                field.appendChild(select);
                if ({$this->getMappingId()}_default_values[\'#{_id}\']) {
                    $(select).setValue({$this->getMappingId()}_default_values[\'#{_id}\']);
                } else {
                    if ({$this->getMappingId()}_default_value[$(\'select_#{_id}\').value]) {
                        $(select).setValue({$this->getMappingId()}_default_value[$(\'select_#{_id}\').value]);
                    }
                }
            }
        <\/script>
JS;

        return str_replace(["\r", "\n", "\r\n"], "", $html);
    }
}
