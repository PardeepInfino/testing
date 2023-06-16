<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Template;

use Amasty\AltTagGenerator\Api\Data\TemplateInterface;

class Registry
{
    /**
     * @var array
     */
    private $cache = [];

    public function save(TemplateInterface $template): void
    {
        if (!isset($this->cache[$template->getId()])) {
            $this->cache[$template->getId()] = $template;
        }
    }

    public function get(int $id): ?TemplateInterface
    {
        return $this->cache[$id] ?? null;
    }
}
