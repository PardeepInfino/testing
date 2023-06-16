<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\Sitemap\Hreflang;

use Amasty\XmlSitemap\Api\SitemapEntity\SitemapEntitySourceInterface as SitemapEntityInterface;

class GenerateCombination
{
    public function execute(array $data): array
    {
        $hreflangs = $data[SitemapEntityInterface::HREFLANG];
        foreach ($hreflangs as $key => $hreflang) {
            $result[$key] = $data;
            $result[$key][SitemapEntityInterface::LOC] = $hreflang['attributes'][SitemapEntityInterface::HREF];
        }

        return $result ?? [];
    }
}
