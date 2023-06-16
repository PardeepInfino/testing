<?php
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

use Magento\Catalog\Model\Product\Configuration\Item\Option;
use Magento\Downloadable\Helper\Catalog\Product\Configuration;
use Aheadworks\QuickOrder\Model\Product\Checker\Inventory\IsNotSalableForRequestedQtyMessageProvider;

/**
 * Class DownloadableProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class DownloadableProvider extends DefaultProvider
{
    /**
     * @var Configuration
     */
    private $productConfig;

    /**
     * @param IsNotSalableForRequestedQtyMessageProvider $isNotSalableMessageProvider
     * @param Configuration $productConfig
     */
    public function __construct(
        IsNotSalableForRequestedQtyMessageProvider $isNotSalableMessageProvider,
        Configuration $productConfig
    ) {
        parent::__construct($isNotSalableMessageProvider);
        $this->productConfig = $productConfig;
    }

    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        $option = [];

        /** @var Option $option */
        $linkIds = $this->product->getCustomOption('downloadable_link_ids');
        if ($linkIds) {
            $itemLinks = [];
            $productLinks = $this->product->getTypeInstance()->getLinks($this->product);
            foreach (explode(',', (string)$linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }

            $option[] = $this->prepareOption($itemLinks);
        }

        return $option;
    }

    /**
     * Prepare option
     *
     * @param array $itemLinks
     * @return array
     */
    private function prepareOption($itemLinks)
    {
        $value = [];
        foreach ($itemLinks as $link) {
            $value[] = $link->getTitle();
        }

        return [
            'label' => $this->productConfig->getLinksTitle($this->product),
            'value' => implode(', ', $value)
        ];
    }
}
