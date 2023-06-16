<?php
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Aheadworks\QuickOrder\Model\Product\Checker\Configurable as ConfigurableProductChecker;
use Aheadworks\QuickOrder\Model\Product\Checker\Inventory\IsNotSalableForRequestedQtyMessageProvider;

/**
 * Class ConfigurableProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class ConfigurableProvider extends AbstractProvider
{
    /**
     * @var ConfigurableProductChecker
     */
    private $configurableProductChecker;

    /**
     * @param IsNotSalableForRequestedQtyMessageProvider $isNotSalableMessageProvider
     * @param ConfigurableProductChecker $configurableProductChecker
     */
    public function __construct(
        IsNotSalableForRequestedQtyMessageProvider $isNotSalableMessageProvider,
        ConfigurableProductChecker $configurableProductChecker
    ) {
        parent::__construct($isNotSalableMessageProvider);
        $this->configurableProductChecker = $configurableProductChecker;
    }

    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($orderOptions)
    {
        return isset($orderOptions['attributes_info']) ? array_values($orderOptions['attributes_info']) : [];
    }

    /**
     * @inheritdoc
     */
    public function getProductForImage()
    {
        $productForImage = $this->getProduct();
        $childProduct = $this->getChildProduct();
        if ($childProduct !== null
            && $this->configurableProductChecker->isNeedToUseChildProductImage($childProduct)
        ) {
            $productForImage = $childProduct;
        }
        return $productForImage;
    }

    /**
     * @inheritdoc
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getQtySalableMessage($requestedQty)
    {
        $product = $this->getChildProduct();
        if (!$product) {
            return '';
        }

        return $this->getIsNotSalableMessageForRequestedQty($product, $requestedQty);
    }

    /**
     * Retrieve child product
     *
     * @return ProductInterface|Product|null
     */
    protected function getChildProduct()
    {
        return !empty($this->subProducts) ? reset($this->subProducts) : null;
    }
}
