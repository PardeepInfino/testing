<?php
namespace Aheadworks\QuickOrder\Model\Product\DetailProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Aheadworks\QuickOrder\Model\Exception\OperationException;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Model\Product\Option\Converter as OptionConverter;

/**
 * Class Pool
 *
 * @package Aheadworks\QuickOrder\Model\Product\DetailProvider
 */
class Pool
{
    /**
     * Provider default type
     */
    const TYPE_DEFAULT = 'default';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var OptionConverter
     */
    private $optionConverter;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $typeList = [];

    /**
     * @var array
     */
    private $providerRegistry = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductRepositoryInterface $productRepository
     * @param OptionConverter $optionConverter
     * @param array $typeList
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductRepositoryInterface $productRepository,
        OptionConverter $optionConverter,
        array $typeList = []
    ) {
        $this->objectManager = $objectManager;
        $this->productRepository = $productRepository;
        $this->optionConverter = $optionConverter;
        $this->typeList = $typeList;
    }

    /**
     * Get product details provider
     *
     * @param array $item
     * @return AbstractProvider
     * @throws OperationException
     */
    public function get($item)
    {
        if (!$item[ProductListItemInterface::ITEM_KEY]) {
            return $this->getWithoutCaching($item);
        }
        if (!isset($this->providerRegistry[$item[ProductListItemInterface::ITEM_KEY]])) {
            $productDetailsProvider = $this->getWithoutCaching($item);
            $this->providerRegistry[$item[ProductListItemInterface::ITEM_KEY]] = $productDetailsProvider;
        }

        return $this->providerRegistry[$item[ProductListItemInterface::ITEM_KEY]];
    }

    /**
     * Prepare product details provider
     *
     * @param array $item
     * @return AbstractProvider
     * @throws OperationException
     */
    public function getWithoutCaching($item)
    {
        $product = $this->loadProduct($item);
        $productType = $product->getTypeId();
        /** @var AbstractProvider $productDetailsProvider */
        $productDetailsProvider = $this->create($productType);
        $type = $product->getTypeInstance();
        $productDetailsProvider->setProduct($product);
        if (isset($item[ProductListItemInterface::PRODUCT_OPTION])) {
            $buyRequest = $this->optionConverter->toBuyRequest(
                $productType,
                $item[ProductListItemInterface::PRODUCT_OPTION]
            );
            $result = $type->prepareForCartAdvanced($buyRequest, $product, AbstractType::PROCESS_MODE_FULL);
            if (is_string($result) || $result instanceof Phrase) {
                $productDetailsProvider->setProductPreparationError($result);
            } else {
                $productDetailsProvider->resolveAndSetSubProducts($result);
            }
        }

        return $productDetailsProvider;
    }

    /**
     * Create product details provider instance
     *
     * @param string $productType
     * @return AbstractProvider
     */
    public function create($productType)
    {
        $className = isset($this->typeList[$productType])
            ? $this->typeList[$productType]
            : $this->typeList[self::TYPE_DEFAULT];

        $instance = $this->objectManager->create($className);
        return $instance;
    }

    /**
     * Load product
     *
     * @param array $item
     * @return ProductInterface|Product
     * @throws OperationException
     */
    private function loadProduct($item)
    {
        try {
            if (isset($item[ProductListItemInterface::PRODUCT_ID])) {
                return $this->productRepository->getById(
                    $item[ProductListItemInterface::PRODUCT_ID],
                    false,
                    null,
                    true
                );
            }
            if (isset($item[ProductListItemInterface::PRODUCT_SKU])) {
                return $this->productRepository->get(
                    $item[ProductListItemInterface::PRODUCT_SKU],
                    false,
                    null,
                    true
                );
            }
        } catch (NoSuchEntityException $exception) {
            throw new OperationException(__($exception->getMessage()));
        }

        throw new OperationException(__('Product is not provided'));
    }
}
