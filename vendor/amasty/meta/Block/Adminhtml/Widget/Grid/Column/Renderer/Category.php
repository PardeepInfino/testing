<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */
namespace Amasty\Meta\Block\Adminhtml\Widget\Grid\Column\Renderer;
class Category
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        return $row->getData('category_name');
    }
}
