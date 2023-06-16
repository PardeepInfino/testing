<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;

/**
 * Class Comparator
 *
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item
 */
class Comparator
{
    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @param OptionConverter $optionConverter
     */
    public function __construct(
        OptionConverter $optionConverter
    ) {
        $this->optionConverter = $optionConverter;
    }

    /**
     * Compare two requisition list items
     *
     * @param RequisitionListItemInterface $target
     * @param RequisitionListItemInterface $source
     * @return bool
     */
    public function compareIfEqual($target, $source)
    {
        if ($target->getProductSku() != $source->getProductSku()) {
            return false;
        }

        $targetBuyRequest = $this->optionConverter->toBuyRequest(
            $target->getProductType(),
            $target->getProductOption()
        )->getData();
        $sourceBuyRequest = $this->optionConverter->toBuyRequest(
            $source->getProductType(),
            $source->getProductOption()
        )->getData();

        if ($targetBuyRequest != $sourceBuyRequest) {
            return false;
        }

        return true;
    }
}
