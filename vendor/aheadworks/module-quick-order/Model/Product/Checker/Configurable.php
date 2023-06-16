<?php
namespace Aheadworks\QuickOrder\Model\Product\Checker;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ConfigurableProduct\Model\Product\Configuration\Item\ItemProductResolver
    as ConfigurableProductConfigurationItemProductResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Configurable
 *
 * @package Aheadworks\QuickOrder\Model\Product\Checker
 */
class Configurable
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if need to use child product image
     * ref: \Magento\ConfigurableProduct\Model\Product\Configuration\Item\ItemProductResolver::isUseChildProduct
     *
     * @param ProductInterface|Product $childProduct
     * @return bool
     */
    public function isNeedToUseChildProductImage($childProduct)
    {
        $thumbnailSourceConfigValue = $this->scopeConfig->getValue(
            ConfigurableProductConfigurationItemProductResolver::CONFIG_THUMBNAIL_SOURCE,
            ScopeInterface::SCOPE_STORE
        );
        $childProductThumbnail = $childProduct->getData('thumbnail');
        return $thumbnailSourceConfigValue !== Thumbnail::OPTION_USE_PARENT_IMAGE
            && $childProductThumbnail !== null
            && $childProductThumbnail !== 'no_selection';
    }
}
