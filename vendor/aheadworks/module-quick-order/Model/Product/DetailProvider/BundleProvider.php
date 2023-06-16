<?php
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

/**
 * Class BundleProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class BundleProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['bundle_options']) ? array_values($orderOptions['bundle_options']) : [];
    }

    /**
     * @inheritdoc
     */
    public function getQtySalableMessage($requestedQty)
    {
        $message = '';
        foreach ($this->subProducts as $product) {
            $qty = $product->getCartQty() * $requestedQty;
            $resultMessage = $this->getIsNotSalableMessageForRequestedQty($product, $qty);
            if ($resultMessage) {
                $message = $resultMessage;
                break;
            }
        }

        return $message;
    }
}
