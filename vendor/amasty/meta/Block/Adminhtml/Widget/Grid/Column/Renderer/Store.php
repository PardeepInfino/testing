<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */
namespace Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Renderer;

class Store
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Store

{
    public function render(\Magento\Framework\DataObject $row)
    {
        $out = parent::render($row);
        if (empty($out)) {
            return __('Default');
        }

        return $out;
    }
}
