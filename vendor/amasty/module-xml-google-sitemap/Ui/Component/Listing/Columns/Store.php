<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Ui\Component\Listing\Columns;

class Store extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if (array_key_exists($this->storeKey, $item) && !is_array($item[$this->storeKey])) {
            $item[$this->storeKey] = [$item[$this->storeKey]];
        }

        return parent::prepareItem($item);
    }
}
