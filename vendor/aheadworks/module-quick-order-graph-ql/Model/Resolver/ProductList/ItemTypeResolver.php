<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;

/**
 * Class ItemTypeResolver
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList
 */
class ItemTypeResolver implements TypeResolverInterface
{
    /**
     * @var array
     */
    private $supportedTypes = [];

    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @param ProductDetailPool $productDetailPool
     * @param array $supportedTypes
     */
    public function __construct(
        ProductDetailPool $productDetailPool,
        array $supportedTypes = []
    ){
        $this->productDetailPool = $productDetailPool;
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * @inheritdoc
     */
    public function resolveType(array $data) : string
    {
        if (!isset($data['product_type'])) {
            throw new LocalizedException(__('Missing key "product_type" in item data'));
        }
        $productTypeId = $data['product_type'];

        if (!isset($this->supportedTypes[$productTypeId])) {
            throw new LocalizedException(
                __('Product "%product_type" type is not supported', ['product_type' => $productTypeId])
            );
        }

        return $this->supportedTypes[$productTypeId];
    }
}
