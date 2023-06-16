<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList\Item;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;

/**
 * Class Price
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList\Item
 */
class Price implements ResolverInterface
{
    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @param ProductDetailPool $productDetailPool
     * @param PriceHelper $priceHelper
     */
    public function __construct(
        ProductDetailPool $productDetailPool,
        PriceHelper $priceHelper
    ) {
        $this->productDetailPool = $productDetailPool;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $provider = $this->productDetailPool->get($value);
        $price = $provider->getProduct()->getFinalPrice();
        return $this->priceHelper->currency($price);
    }
}
