<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\OptionSource;

use Magento\Directory\Model\Config\Source\Country\Full as CountrySource;
use Magento\Framework\Data\OptionSourceInterface;

class Country implements OptionSourceInterface
{
    const DONT_ADD = '0';
    const DEFAULT_VALUE = '1';

    /**
     * @var CountrySource
     */
    private $countrySource;

    public function __construct(CountrySource $countrySource)
    {
        $this->countrySource = $countrySource;
    }

    public function toOptionArray(): array
    {
        $options = [
            ['value' => self::DONT_ADD, 'label' => __("Don't Add")],
            ['value' => self::DEFAULT_VALUE, 'label' => __('From Current Store Default Country')]
        ];
        $countries = array_map(function ($row) {
            $row['label'] .= ' (' . $row['value'] . ')';

            return $row;
        }, $this->countrySource->toOptionArray());

        return $options + $countries;
    }
}
