<?php
namespace Aheadworks\QuickOrder\Model\Product\Option;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Catalog\Api\Data\ProductOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductOptionExtensionFactory;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Converter
 *
 * @package Aheadworks\QuickOrder\Model\Product\Option
 */
class Converter
{
    /**
     * Quick order param
     */
    const AW_QUICK_ORDER_PARAM = 'aw_quick_order';

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory
     */
    private $productOptionExtensionFactory;

    /**
     * @var ProcessorPool
     */
    private $processorPool;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param ProductOptionInterfaceFactory $productOptionFactory
     * @param ProductOptionExtensionFactory $productOptionExtensionFactory
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        ProductOptionInterfaceFactory $productOptionFactory,
        ProductOptionExtensionFactory $productOptionExtensionFactory,
        ProcessorPool $processorPool
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionExtensionFactory = $productOptionExtensionFactory;
        $this->processorPool = $processorPool;
    }

    /**
     * Convert to product option object
     *
     * @param string $productType
     * @param array $buyRequest
     * @return ProductOptionInterface
     */
    public function toProductOptionObject($productType, $buyRequest)
    {
        $request = $this->dataObjectFactory->create();
        $request->addData($buyRequest);
        $request->setData(self::AW_QUICK_ORDER_PARAM, true);

        /** @var ProductOptionInterface $productOption */
        $productOption = $this->productOptionFactory->create();
        $productTypeProcessor = $this->processorPool->get($productType);
        if ($productTypeProcessor) {
            $optionData = $productTypeProcessor->convertToProductOption($request);
            if ($optionData) {
                $this->applyDataToOption($productOption, $optionData);
            }
        }

        $customOptionsProcessor = $this->processorPool->get('custom_options');
        if ($customOptionsProcessor) {
            $optionData = $customOptionsProcessor->convertToProductOption($request);
            if ($optionData) {
                $this->applyDataToOption($productOption, $optionData);
            }
        }

        return $productOption;
    }

    /**
     * Convert to product using by request
     *
     * @param string $productType
     * @param ProductOptionInterface $productOption
     * @return DataObject
     */
    public function toBuyRequest($productType, $productOption)
    {
        /** @var DataObject $request */
        $request = $this->dataObjectFactory->create();

        $productTypeProcessor = $this->processorPool->get($productType);
        if ($productTypeProcessor) {
            $requestUpdate = $productTypeProcessor->convertToBuyRequest($productOption);
            $request->addData($requestUpdate->getData());
        }

        $customOptionsProcessor = $this->processorPool->get('custom_options');
        if ($customOptionsProcessor) {
            $requestUpdate = $customOptionsProcessor->convertToBuyRequest($productOption);
            $request->addData($requestUpdate->getData());
        }

        return $request;
    }

    /**
     * Apply data to option
     *
     * @param ProductOptionInterface $option
     * @param array $data
     */
    private function applyDataToOption(ProductOptionInterface $option, $data)
    {
        $extensionAttributes = $option->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->productOptionExtensionFactory->create();
            $option->setExtensionAttributes($extensionAttributes);
        }

        $extensionAttributes->setData(key($data), current($data));
    }
}
