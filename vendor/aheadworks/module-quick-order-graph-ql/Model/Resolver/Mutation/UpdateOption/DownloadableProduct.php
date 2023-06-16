<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption;

/**
 * Class DownloadableProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class DownloadableProduct extends DefaultProduct
{
    /**
     * @inheritdoc
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        $buyRequest = parent::prepareBuyRequest($buyRequest, $optionsData);
        if (isset($optionsData['links']) && is_array($optionsData['links'])) {
            $buyRequest['links'] = [];
            foreach ($optionsData['links'] as $link) {
                $buyRequest['links'][] = $link['link_id'];
            }
        }

        return $buyRequest;
    }
}
