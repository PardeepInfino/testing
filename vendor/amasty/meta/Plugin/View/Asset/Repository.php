<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */

namespace Amasty\Meta\Plugin\View\Asset;

class Repository
{
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $data
    ) {
        $this->data = $data;
    }

    public function aroundCreateRemoteAsset(
        $subject,
        \Closure $proceed,
        $url,
        $contentType
    ) {
        $result = $proceed($url, $contentType);

        $canonical = $this->data->getReplaceData('custom_canonical_url');

        if ($contentType=='canonical' && $canonical) {
            $result = $proceed($canonical, $contentType);
        }

        return $result;
    }
}
