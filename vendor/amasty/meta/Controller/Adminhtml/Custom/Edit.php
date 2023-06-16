<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */

namespace Amasty\Meta\Controller\Adminhtml\Custom;

class Edit extends \Amasty\Meta\Controller\Adminhtml\Config\Edit
{
    /**
     * @var string
     */
    protected $paramName = 'id';

    /**
     * @var string
     */
    protected $_blockName = 'Custom';
}
