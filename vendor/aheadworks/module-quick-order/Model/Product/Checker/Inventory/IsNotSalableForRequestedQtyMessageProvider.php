<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker\Inventory;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class IsNotSalableForRequestedQtyMessageProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker\Inventory
 */
class IsNotSalableForRequestedQtyMessageProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var IsNotSalableResultFactory
     */
    private $isNotSalableResultFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param IsNotSalableResultFactory $isNotSalableResultFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        IsNotSalableResultFactory $isNotSalableResultFactory
    ) {
        $this->storeManager = $storeManager;
        $this->isNotSalableResultFactory = $isNotSalableResultFactory;
    }

    /**
     * Get result message
     *
     * Returns error message in case product is not salable for requested qty
     *
     * @param Product|ProductInterface $product
     * @param float|int $requestedQty
     * @return string
     */
    public function getResultMessage($product, $requestedQty)
    {
        $isNotSalableResult = $this->isNotSalableResultFactory->create();
        return $isNotSalableResult->getResultMessage($product, $requestedQty);
    }
}
