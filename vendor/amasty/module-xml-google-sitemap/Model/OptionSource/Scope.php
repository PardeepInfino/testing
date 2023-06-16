<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class Scope implements OptionSourceInterface
{
    const SCOPE_GLOBAL = '0';
    const SCOPE_WEBSITE = '1';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::SCOPE_GLOBAL, 'label' => __('Global')],
            ['value' => self::SCOPE_WEBSITE, 'label' => __('Website')]
        ];
    }
}
