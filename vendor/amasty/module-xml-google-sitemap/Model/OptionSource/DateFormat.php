<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class DateFormat implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->toArray() as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }

    public function toArray(): array
    {
        return [
            'Y-m-d\TH:i:sP' => __('With time'),
            'Y-m-d' => __('Without time'),
        ];
    }
}
