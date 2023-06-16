<?php
namespace Aheadworks\QuickOrder\Plugin\Model\EnterpriseGiftCard;

use Magento\Framework\DataObject;

/**
 * Class ProductOptionProcessorPlugin
 *
 * @package Aheadworks\QuickOrder\Plugin\Model\EnterpriseGiftCard
 */
class ProductOptionProcessorPlugin
{
    /**
     * Convert extra buy request params to product option
     *
     * @param \Magento\GiftCard\Model\ProductOptionProcessor $subject
     * @param array $resultArray
     * @param DataObject $buyRequest
     * @return array
     */
    public function afterConvertToProductOption($subject, $resultArray, $buyRequest)
    {
        if ($buyRequest->getCustomGiftcardAmount()
            && $buyRequest->getAwQuickOrder()
            && isset($resultArray['giftcard_item_option'])
        ) {
            /** @var \Magento\GiftCard\Api\Data\GiftCardOptionInterface $giftCardOption */
            $giftCardOption = $resultArray['giftcard_item_option'];
            $giftCardOption->setCustomGiftcardAmount($buyRequest->getCustomGiftcardAmount());
            $resultArray['giftcard_item_option'] = $giftCardOption;
        }

        return $resultArray;
    }
}
