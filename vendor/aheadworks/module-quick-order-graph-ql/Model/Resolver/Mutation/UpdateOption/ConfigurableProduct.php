<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption;

/**
 * Class ConfigurableProduct
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\Mutation\UpdateOption
 */
class ConfigurableProduct extends DefaultProduct
{
    /**
     * @inheritdoc
     */
    public function prepareBuyRequest($buyRequest, $optionsData)
    {
        $buyRequest = parent::prepareBuyRequest($buyRequest, $optionsData);
        if (isset($optionsData['super_attribute']) && is_array($optionsData['super_attribute'])) {
            $buyRequest['super_attribute'] = [];
            foreach ($optionsData['super_attribute'] as $superAttribute) {
                $buyRequest['super_attribute'][$superAttribute['option_id']] = $superAttribute['option_value'];
            }
        }

        return $buyRequest;
    }
}
