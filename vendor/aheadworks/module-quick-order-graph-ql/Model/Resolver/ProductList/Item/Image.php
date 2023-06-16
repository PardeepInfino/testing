<?php
namespace Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList\Item;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Helper\Image as ImageHelper;
use Aheadworks\QuickOrder\Model\Product\DetailProvider\Pool as ProductDetailPool;
use Aheadworks\QuickOrder\Model\Product\Search\ResultProvider;

/**
 * Class Image
 *
 * @package Aheadworks\QuickOrderGraphQl\Model\Resolver\ProductList\Item
 */
class Image implements ResolverInterface
{
    /**
     * @var ProductDetailPool
     */
    private $productDetailPool;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @param ProductDetailPool $productDetailPool
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        ProductDetailPool $productDetailPool,
        ImageHelper $imageHelper
    ) {
        $this->productDetailPool = $productDetailPool;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $provider = $this->productDetailPool->get($value);
        $imageHelper = $this->imageHelper->init(
            $provider->getProductForImage(),
            ResultProvider::IMAGE_ID
        );

        return [
            'image_url' => $imageHelper->getUrl(),
            'name_url' => $provider->getProductUrl(),
            'image_label' => $imageHelper->getLabel()
        ];
    }
}
