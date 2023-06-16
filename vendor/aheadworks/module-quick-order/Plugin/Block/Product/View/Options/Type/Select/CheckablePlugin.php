<?php
namespace Aheadworks\QuickOrder\Plugin\Block\Product\View\Options\Type\Select;

use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Block\Product\View\Options\Type\Select\Checkable;

/**
 * Class CheckablePlugin
 *
 * @package Aheadworks\QuickOrder\Plugin\Block\Product\View\Options\Type\Select
 */
class CheckablePlugin
{
    /**
     * Format correct result of configured value
     *
     * In case of checkbox type of option magento option processor (ProductOptionProcessor)
     * incorrectly converts buy request to product option and vise versa
     *
     * @param Checkable $subject
     * @param string|array|null $result
     * @param Option $option
     * @return string|array|null
     */
    public function afterGetPreconfiguredValue($subject, $result, Option $option)
    {
        if ($option->getType() == Option::OPTION_TYPE_CHECKBOX) {
            $result = !is_array($result) ? explode(',', (string)$result) : $result;
        }

        return $result;
    }
}
