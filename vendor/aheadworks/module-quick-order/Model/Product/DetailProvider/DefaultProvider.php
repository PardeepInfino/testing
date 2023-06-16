<?php
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

/**
 * Class DefaultProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class DefaultProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($productOption)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getQtySalableMessage($requestedQty)
    {
        return $this->getIsNotSalableMessageForRequestedQty($this->getProduct(), $requestedQty);
    }
}
