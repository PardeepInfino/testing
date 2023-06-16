<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Settings\Cron;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Options
 */
class Options implements ArrayInterface
{
    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Magento CRON')],
            ['value' => '2', 'label' => __('Command Line (CLI) CRON')]
        ];
    }
}