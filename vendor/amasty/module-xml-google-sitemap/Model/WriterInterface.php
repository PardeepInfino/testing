<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model;

use Generator;

interface WriterInterface
{
    const PART_HEADER = 'header';
    const PART_FOOTER = 'footer';

    /**
     * @param Generator $data
     * @param array $parts
     */
    public function write(Generator $data, array $parts): void;

    /**
     * @param string $filePath
     */
    public function open(string $filePath): void;
}
