<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface SitemapRepositoryInterface
{
    /**
     * @param SitemapInterface $sitemap
     *
     * @return SitemapInterface
     */
    public function save(SitemapInterface $sitemap): SitemapInterface;

    /**
     * @param int $id
     *
     * @return SitemapInterface
     *
     * @throws NoSuchEntityException
     */
    public function getById(int $id): SitemapInterface;

    /**
     * @param SitemapInterface $sitemap
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(SitemapInterface $sitemap): bool;

    /**
     * @param int $id
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
