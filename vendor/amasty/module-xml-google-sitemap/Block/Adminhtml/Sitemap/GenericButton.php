<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap;

use Amasty\XmlSitemap\Api\SitemapInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class GenericButton
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Registry
     *
     * @var Registry
     */
    private $registry;

    public function __construct(
        UrlInterface $urlBuilder,
        Registry $registry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
    }

    public function getUrlBuilder()
    {
        return $this->urlBuilder;
    }

    /**
     * @return null|int
     */
    public function getSitemapId(): ?int
    {
        $sitemap = $this->registry->registry(SitemapInterface::PERSIST_NAME);

        return $sitemap ? (int) $sitemap->getSitemapId() : null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     *
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
