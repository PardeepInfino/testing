<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\Writer;

use Amasty\XmlSitemap\Model\FileWriter;
use Magento\Framework\ObjectManagerInterface;
use Amasty\XmlSitemap\Model\WriterInterface;

class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(array $config): WriterInterface
    {
        $writerClass = $config['writer_class'] ?? FileWriter::class;

        return $this->objectManager->create($writerClass, ['writerConfig' => $config]);
    }
}
