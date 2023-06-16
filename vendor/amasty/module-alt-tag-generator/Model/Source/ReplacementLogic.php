<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Alt Tag Generator for Magento 2 (System)
 */

namespace Amasty\AltTagGenerator\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ReplacementLogic implements OptionSourceInterface
{
    public const REPLACE = 0;
    public const REPLACE_EMPTY = 1;
    public const APPEND = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::REPLACE, 'label' => __('Replace Filled Alt Text')],
            ['value' => self::REPLACE_EMPTY, 'label' => __('Only Replace Empty Alt Text')],
            ['value' => self::APPEND, 'label' => __('Append to Existing Alt Text')]
        ];
    }
}
