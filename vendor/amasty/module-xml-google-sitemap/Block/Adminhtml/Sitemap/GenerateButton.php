<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Block\Adminhtml\Sitemap;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class GenerateButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $sitemapId = $this->getSitemapId();
        if ($sitemapId) {
            $data = [
                'label'      => __('Generate'),
                'class'      => 'generate',
                'on_click'   => 'setLocation(\'' .
                    $this->getUrlBuilder()->getUrl('*/*/generate', ['sitemap_id' => $sitemapId]) .
                    '\')',
                'sort_order' => 4,
            ];
        }

        return $data;
    }
}
