<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class CmsRelation implements OptionSourceInterface
{
    const ID = 'page_id';
    const UUID = 'amseo-uuid';
    const IDENTIFIER = 'identifier';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::ID, 'label' => __('By ID')],
            ['value' => self::UUID, 'label' => __('By Hreflang UUID')],
            ['value' => self::IDENTIFIER, 'label' => __('By URL Key (Page Identifier)')]
        ];
    }
}
