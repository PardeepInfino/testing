<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Indexer\Template\Action;

use Amasty\AltTagGenerator\Model\ResourceModel\TemplateIndex\TableWorker;
use Amasty\AltTagGenerator\Model\ResourceModel\TemplateIndex;
use Exception;

class ExecutePartial
{
    /**
     * @var DoReindex
     */
    private $doReindex;

    /**
     * @var TableWorker
     */
    private $tableWorker;

    public function __construct(DoReindex $doReindex, TableWorker $tableWorker)
    {
        $this->doReindex = $doReindex;
        $this->tableWorker = $tableWorker;
    }

    /**
     * @param array|null $templateIds
     * @param array|null $productIds
     * @return void
     * @throws Exception
     */
    public function execute(?array $templateIds = null, ?array $productIds = null): void
    {
        $this->tableWorker->createTemporaryTable();

        $this->doReindex->execute($templateIds, $productIds);

        if ($templateIds !== null) {
            $fieldName = TemplateIndex::TEMPLATE_ID;
            $ids = $templateIds;
        } else {
            $fieldName = TemplateIndex::PRODUCT_ID;
            $ids = $productIds;
        }

        $this->tableWorker->syncDataPartial([
            sprintf('%s IN (?)', $fieldName) => $ids
        ]);
    }
}
