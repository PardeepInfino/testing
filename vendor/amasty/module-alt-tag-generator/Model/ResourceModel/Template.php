<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\ResourceModel;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Template extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(TemplateInterface::MAIN_TABLE, TemplateInterface::ID);
    }
}
