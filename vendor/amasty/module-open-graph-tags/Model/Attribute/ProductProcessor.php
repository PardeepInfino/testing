<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Open Graph Tags for Magento 2 (System)
 */

namespace Amasty\OpenGraphTags\Model\Attribute;

use Amasty\OpenGraphTags\Model\ConfigProvider;
use Amasty\OpenGraphTags\Model\Meta\GetReplacedMetaData;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Page\Config as PageConfig;
use Psr\Log\LoggerInterface;

class ProductProcessor
{
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetReplacedMetaData
     */
    private $getReplacedMetaData;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ConfigProvider $configProvider,
        LoggerInterface $logger,
        GetReplacedMetaData $getReplacedMetaData,
        PageConfig $pageConfig
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->getReplacedMetaData = $getReplacedMetaData;
        $this->pageConfig = $pageConfig;
    }

    public function getProductTitleAttributeValue(Product $product): string
    {
        $attributeCode = $this->configProvider->getProductPageTitleAttribute();

        return $this->getAttributeValue($attributeCode, $product) ?: $this->pageConfig->getTitle()->get();
    }

    public function getProductDescriptionAttributeValue(Product $product): ?string
    {
        $attributeCode = $this->configProvider->getProductPageDescriptionAttribute();

        return $this->getAttributeValue($attributeCode, $product) ?: $this->pageConfig->getDescription();
    }

    private function getAttributeValue(string $attributeCode, Product $product): string
    {
        try {
            $attribute = $this->productAttributeRepository->get($attributeCode);
        } catch (NoSuchEntityException $e) {
            $message = __('Amasty Open Graph Tags: %1', $e->getMessage());
            $this->logger->debug($message);

            return '';
        }

        $metaValue = $this->getReplacedMetaData->execute($attributeCode);

        if ($metaValue) {
            $value = $metaValue;
        } elseif ($attribute->usesSource()) {
            $value = $product->getAttributeText($attributeCode);

            if (is_array($value)) {
                $value = implode(',', $value);
            }
        } else {
            $value = $product->getData($attributeCode);
        }

        return (string)$value;
    }
}
