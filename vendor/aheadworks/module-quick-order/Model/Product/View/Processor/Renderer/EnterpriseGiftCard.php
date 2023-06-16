<?php
namespace Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer;

use Magento\Framework\View\Element\Template;

/**
 * Class EnterpriseGiftCard
 *
 * @package Aheadworks\QuickOrder\Model\Product\View\Processor\Renderer
 */
class EnterpriseGiftCard extends AbstractRenderer implements RendererInterface
{
    /**
     * Gift card product type ID
     */
    const TYPE_ID = 'giftcard';

    /**
     * Gift card block with fields
     */
    const GIFT_CARD_BLOCK = 'product.info.giftcard';

    /**
     * Gift card block with gift card price
     */
    const GIFT_CARD_FINAL_PRICE_BLOCK = 'product.price.final';

    /**
     * @inheritdoc
     */
    public function render($layout, $block, $product)
    {
        if ($product->getTypeId() != self::TYPE_ID) {
            return $this;
        }

        $finalPriceGiftCard = $layout->getBlock(self::GIFT_CARD_FINAL_PRICE_BLOCK);
        if ($finalPriceGiftCard instanceof Template) {
            $this->appendBlock($block, $finalPriceGiftCard, 'ee_gift_card_final_price');
        }

        $giftCardBlock = $layout->getBlock(self::GIFT_CARD_BLOCK);
        if ($giftCardBlock instanceof Template) {
            $this->appendBlock($block, $giftCardBlock, 'ee_gift_card');
        }

        return $this;
    }
}
