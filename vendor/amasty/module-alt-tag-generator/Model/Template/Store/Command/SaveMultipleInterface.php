<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template\Store\Command;

use Zend_Db_Exception;

interface SaveMultipleInterface
{
    /**
     * @param int $templateId
     * @param array $stores
     * @return bool Return true if database changed.
     * @throws Zend_Db_Exception
     */
    public function execute(int $templateId, array $stores): bool;
}
