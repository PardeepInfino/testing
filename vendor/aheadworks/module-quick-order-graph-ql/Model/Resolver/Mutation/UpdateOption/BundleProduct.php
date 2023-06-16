<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption;

/**
 * Class BundleProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class BundleProduct extends DefaultProduct
{
    /**
     * @inheritdoc
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        $buyRequest = parent::prepareBuyRequest($buyRequest, $optionsData);
        if (isset($optionsData['bundle_options']) && is_array($optionsData['bundle_options'])) {
            $buyRequest['bundle_option'] = [];
            $buyRequest['bundle_option_qty'] = [];
            foreach ($optionsData['bundle_options'] as $bundleOption) {
                $buyRequest['bundle_option'][$bundleOption['id']] = $bundleOption['value'];
                $buyRequest['bundle_option_qty'][$bundleOption['id']] = $bundleOption['quantity'];
            }
        }

        return $buyRequest;
    }
}
