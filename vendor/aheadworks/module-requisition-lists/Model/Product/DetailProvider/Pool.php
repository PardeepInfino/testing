<?php
namespace Aheadworks\RequisitionLists\Model\Product\DetailProvider;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListItemRepositoryInterface;
use Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options\Converter as OptionConverter;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class Pool
 *
 * @package Aheadworks\RequisitionLists\Model\Product\DetailProvider
 */
class Pool
{
    /**
     * Provider type
     */
    const PROVIDER_TYPE_DEFAULT = 'simple';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    private $providers = [];

    /**
     * @var RequisitionListItemRepositoryInterface
     */
    private $requisitionListItemRepository;

    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @var Grouped
     */
    private $groupedType;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SimpleProvider $simpleProvider
     * @param RequisitionListItemRepositoryInterface $requisitionListItemRepository
     * @param OptionConverter $optionConverter
     * @param array $providers
     */
    public function __construct(
        Grouped $groupedType,
        ProductRepositoryInterface $productRepository,
        SimpleProvider $simpleProvider,
        RequisitionListItemRepositoryInterface $requisitionListItemRepository,
        OptionConverter $optionConverter,
        array $providers = []
    ) {
        $this->productRepository = $productRepository;
        $providers = array_merge($providers, [self::PROVIDER_TYPE_DEFAULT => $simpleProvider]);
        $this->providers = $providers;
        $this->requisitionListItemRepository = $requisitionListItemRepository;
        $this->optionConverter = $optionConverter;
        $this->groupedType = $groupedType;
    }

    /**
     * Get product details provider
     *
     * @param array $itemData
     *
     * @return AbstractProvider
     * @throws LocalizedException
     */
    public function getProvider($itemData)
    {
        $productId = $itemData[RequisitionListItemInterface::PRODUCT_ID];
        $product = $this->productRepository->getById($productId, false, null, true);

        $groupedParentIds = $this->groupedType->getParentIdsByChild($productId);
        if ($groupedParentIds) {
            $parentProduct = $this->productRepository->getById(reset($groupedParentIds), false, null, true);
        }
        $productType = $itemData[RequisitionListItemInterface::PRODUCT_TYPE];
        $provider = isset($this->providers[$productType])
            ? $this->providers[$productType]
            : $this->providers[self::PROVIDER_TYPE_DEFAULT];

        $provider->setProduct($product, $itemData);
        if (isset($itemData[RequisitionListItemInterface::PRODUCT_OPTION])) {
            $buyRequest = $this->optionConverter->toBuyRequest(
                $product->getTypeId(),
                $itemData[RequisitionListItemInterface::PRODUCT_OPTION]
            );

            $result = $product->getTypeInstance()->prepareForCartAdvanced(
                $buyRequest,
                $product,
                AbstractType::PROCESS_MODE_FULL
            );

            if (is_string($result) || $result instanceof Phrase) {
                $provider->setIsError(true);
            } else {
                $provider->setIsError(false);
                $provider->resolveSubProducts($result);
                if (isset($parentProduct)) {
                    $provider->setParentProduct($parentProduct);
                }
            }
        }

        return $provider;
    }
}
