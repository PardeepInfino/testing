<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\ResourceModel\Sitemap\Actions;

use Amasty\XmlSitemap\Api\SitemapInterface;

interface AdditionalActionInterface
{
    /**
     *
     * @example [
     *              8 => SitemapModel,
     *              9 => SitemapModel
     *          ]
     *
     * @param SitemapInterface[] $sitemapArray
     *
     */
    public function execute(array $sitemapArray): void;
}
