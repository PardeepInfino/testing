<?php
namespace Aheadworks\QuickOrder\Model\Product\Option;

use Magento\Framework\DataObject;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Catalog\Api\Data\ProductOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Serializer
 *
 * @package Aheadworks\QuickOrder\Model\Product\Option
 */
class Serializer
{
    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @param JsonSerializer $serializer
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ProductOptionInterfaceFactory $productOptionFactory
     */
    public function __construct(
        JsonSerializer $serializer,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        ProductOptionInterfaceFactory $productOptionFactory
    ) {
        $this->serializer = $serializer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->productOptionFactory = $productOptionFactory;
    }

    /**
     * Convert product option to serialized string
     *
     * @param ProductOptionInterface $productOption
     * @return DataObject
     */
    public function serializeToString($productOption)
    {
        $objectData = $this->dataObjectProcessor->buildOutputDataArray(
            $productOption,
            ProductOptionInterface::class
        );

        return $this->serializer->serialize($objectData);
    }

    /**
     * Convert product option array to serialized string
     *
     * @param array $productOptionArray
     * @return DataObject
     */
    public function serializeOptionArrayToString($productOptionArray)
    {
        return $this->serializer->serialize($productOptionArray);
    }

    /**
     * Convert serialized string to product option object
     *
     * @param string $serializedString
     * @return ProductOptionInterface
     */
    public function unserializeToObject($serializedString)
    {
        /** @var ProductOptionInterface $object */
        $object = $this->productOptionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->serializer->unserialize($serializedString),
            ProductOptionInterface::class
        );

        return $object;
    }

    /**
     * Convert serialized string to data array
     *
     * @param string $serializedString
     * @return array
     */
    public function unserializeToDataArray($serializedString)
    {
        return $this->serializer->unserialize($serializedString);
    }
}
