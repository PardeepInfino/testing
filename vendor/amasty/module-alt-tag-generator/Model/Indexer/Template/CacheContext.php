<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Indexer\Template;

class CacheContext extends \Magento\Framework\Indexer\CacheContext
{
    /**
     * @var int
     */
    private $countElements = 0;

    /**
     * Register entity Ids
     *
     * @param string $cacheTag
     * @param array $ids
     *
     * @return $this
     */
    public function registerEntities($cacheTag, $ids)
    {
        parent::registerEntities($cacheTag, $ids);
        $this->countElements += count($ids);

        return $this;
    }

    public function getSize(): int
    {
        return $this->countElements;
    }

    public function flush(): void
    {
        $this->countElements = 0;
        $this->entities = [];
    }
}
