<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Controller\Adminhtml\Template\Edit;

use Amasty\AltTagGenerator\Controller\Adminhtml\Template\Edit;

class NewConditionHtml extends \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog\NewConditionHtml
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;
}
