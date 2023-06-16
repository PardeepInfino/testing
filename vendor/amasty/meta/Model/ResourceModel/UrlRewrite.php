<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */

namespace Amasty\Meta\Model\ResourceModel;

use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as MagentoUrlRewrite;
use Magento\UrlRewrite\Model\Storage\DbStorage;

class UrlRewrite extends MagentoUrlRewrite
{
    public function deleteByRequestPathAndStore(string $requestPath, int $storeId)
    {
        $this->getConnection()->delete(
            $this->getTable(DbStorage::TABLE_NAME),
            sprintf('target_path = "%s" AND store_id = %s', $requestPath, $storeId)
        );
    }
}
