<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\Sitemap\Hreflang\UrlProvider;

interface UrlProviderInterface
{
    /**
     * @return array [['entity_id' => 1, 'store_id' => 1, 'url' => ''], ...]
     */
    public function execute(array $storeIds, string $entityType, array $entityIds): array;
}
