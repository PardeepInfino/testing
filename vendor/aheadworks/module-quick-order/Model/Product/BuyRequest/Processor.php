<?php
namespace Aheadworks\QuickOrder\Model\Product\BuyRequest;

use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Magento\Framework\DataObject;
use Aheadworks\QuickOrder\Model\Product\Option\Converter as OptionConverter;

/**
 * Class Processor
 *
 * @package Aheadworks\QuickOrder\Model\Product\BuyRequest
 */
class Processor
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
     * Prepare buy request using product list item
     *
     * @param ProductListItemInterface $item
     * @return DataObject
     */
    public function prepareBuyRequest($item)
    {
        $buyRequest = $this->optionConverter->toBuyRequest($item->getProductType(), $item->getProductOption());
        $buyRequest->addData(['qty' => $item->getProductQty()]);

        return $buyRequest;
    }
}
