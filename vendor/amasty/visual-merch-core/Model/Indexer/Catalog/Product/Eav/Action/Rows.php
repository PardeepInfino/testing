<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Visual Merchandiser Core by Amasty for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\VisualMerchCore\Model\Indexer\Catalog\Product\Eav\Action;

use Amasty\VisualMerchCore\Model\Indexer\Catalog\Product\Eav\IndexAdapter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\InputException;

/**
 * @codeCoverageIgnore
 */
class Rows
{
    /**
     * @var IndexAdapter
     */
    private $indexAdapter;

    public function __construct(IndexAdapter $indexAdapter)
    {
        $this->indexAdapter = $indexAdapter;
    }

    /**
     * @param array $ids
     * @return void
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute(array $ids): void
    {
        if (empty($ids)) {
            throw new InputException(__('Bad value was supplied.'));
        }
        try {
            $ids = $this->indexAdapter->processRelations($ids);
            $this->indexAdapter->getIndexer()->reindexEntities($ids);
            $this->indexAdapter->syncData($ids);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }
}
