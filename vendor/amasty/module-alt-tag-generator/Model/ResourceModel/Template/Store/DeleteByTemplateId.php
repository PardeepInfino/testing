<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\ResourceModel\Template\Store;

use Amasty\AltTagGenerator\Model\ResourceModel\Template\Store\Table as StoreTable;
use Magento\Framework\App\ResourceConnection;

class DeleteByTemplateId
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(int $templateId): void
    {
        $this->resourceConnection->getConnection()->delete(
            $this->resourceConnection->getTableName(StoreTable::NAME),
            [sprintf('%s = ?', StoreTable::TEMPLATE_COLUMN) => $templateId]
        );
    }
}
