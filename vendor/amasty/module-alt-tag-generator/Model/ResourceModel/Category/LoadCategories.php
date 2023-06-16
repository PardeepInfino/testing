<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\ResourceModel\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class LoadCategories
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retrun categories sorted by level desc & exclude root category.
     *
     * @param array $ids
     * @param int|null $limit
     * @return Category[]
     */
    public function execute(array $ids, ?int $limit = null): array
    {
        return $this->collectionFactory->create()
            ->addNameToResult()
            ->addIdFilter($ids)
            ->addFieldToFilter('level', ['gt' => 1])
            ->setOrder('level', Collection::SORT_ORDER_DESC)
            ->setPageSize($limit)
            ->getItems();
    }
}
