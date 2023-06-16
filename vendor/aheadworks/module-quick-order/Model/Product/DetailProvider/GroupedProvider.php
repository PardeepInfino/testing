<?php
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

/**
 * Class GroupedProvider
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class GroupedProvider extends AbstractProvider
{
    /**
     * @inheritdoc
     */
    public function getProductTypeAttributes($productOption)
    {
        $selectedProducts = [];
        foreach ($this->subProducts as $product) {
            $selectedProducts[] = [
                'name' => $product->getName(),
                'qty' => $product->getCartQty()
            ];
        }

        return $selectedProducts;
    }

    /**
     * @inheritdoc
     */
    public function resolveAndSetSubProducts($products)
    {
        $this->subProducts = $products;
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
    public function isQtyEditable()
    {
        return false;
    }
}
