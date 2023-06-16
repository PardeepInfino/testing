<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class Language implements OptionSourceInterface
{
    public const DEFAULT_VALUE = '1';

    /**
     * @var string[]
     */
    private $languageTranslation;

    public function __construct(
        array $languageTranslation
    ) {
        $this->languageTranslation = $languageTranslation;
    }

    public function toOptionArray(): array
    {
        $options = [
            ['value' => self::DEFAULT_VALUE, 'label' => __('From Current Store Locale')]
        ];

        foreach ($this->languageTranslation as $code => $language) {
            $options[] = [
                'value' => $code,
                'label' => $language . ' (' . $code . ')'
            ];
        }

        return $options;
    }
}
