<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Block\Search;

use Magento\Framework\View\Element\Template;

class Popular extends AbstractSearch
{
    public const CATEGORY_BLOCK_POPULAR = 'popular_searches';

    public function getBlockType()
    {
        return self::CATEGORY_BLOCK_POPULAR;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(
            [self::DEFAULT_CACHE_TAG . '_' . $this->getBlockType()],
            Template::getCacheKeyInfo()
        );
    }

    /**
     * @inheritdoc
     */
    public function getResults()
    {
        $result = parent::getResults();
        foreach ($this->getSearchCollection() as $index => $item) {
            $result[$index]['num_results'] = $item->getNumResults();
        }

        return $result;
    }

    /**
     * @return \Magento\Search\Model\ResourceModel\Query\Collection
     */
    protected function generateCollection()
    {
        return $this->queryFactory->get()->getSuggestCollection()
            ->setPageSize($this->getLimit());
    }
    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getName(\Magento\Framework\DataObject $item)
    {
        return $this->generateName($item->getQueryText());
    }

    /**
     * @return bool
     */
    public function isNoFollow()
    {
        return true;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return 3600;
    }
}
