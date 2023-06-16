<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Open Graph Tags for Magento 2 (System)
 */

namespace Amasty\OpenGraphTags\Model\Attribute;

use Amasty\OpenGraphTags\Model\ConfigProvider;
use Magento\Cms\Model\Page;

class CmsProcessor
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Page $cmsPage
     * @return string
     */
    public function getCmsTitleAttributeValue(Page $cmsPage): string
    {
        $attributeCode = $this->configProvider->getCmsPageTitleAttribute();

        return $this->getAttributeValue($attributeCode, $cmsPage);
    }

    /**
     * @param Page $cmsPage
     * @return string
     */
    public function getCmsDescriptionAttributeValue(Page $cmsPage): string
    {
        $attributeCode = $this->configProvider->getCmsPageDescriptionAttribute();

        return $this->getAttributeValue($attributeCode, $cmsPage);
    }

    private function getAttributeValue(string $attributeCode, Page $cmsPage): string
    {
        return $cmsPage->getData($attributeCode) ?: '';
    }
}
