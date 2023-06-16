<?php
namespace Aheadworks\RequisitionLists\Model\Product\Option\Grouped;

use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;
use Aheadworks\RequisitionLists\Api\Data\GroupedItemOptionValueInterface;
use Aheadworks\RequisitionLists\Api\Data\GroupedItemOptionValueInterfaceFactory;

/**
 * Class Processor
 * @package Aheadworks\RequisitionLists\Model\Product\Option\Grouped
 */
class Processor implements ProductOptionProcessorInterface
{
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * @var GroupedItemOptionValueInterfaceFactory
     */
    private $groupedItemOptionValueFactory;

    /**
     * @param DataObjectFactory $objectFactory
     * @param GroupedItemOptionValueInterfaceFactory $groupedItemOptionValueInterfaceFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        GroupedItemOptionValueInterfaceFactory $groupedItemOptionValueInterfaceFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->groupedItemOptionValueFactory = $groupedItemOptionValueInterfaceFactory;
    }

    /**
     * @inheritdoc
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $options = $this->getGroupedItemOptions($productOption);
        if (!empty($options)) {
            $requestData = [];
            foreach ($options as $option) {
                /** @var GroupedItemOptionValueInterface $option */
                $requestData['super_group'][$option->getOptionId()] = $option->getOptionValue();
            }
            $request->addData($requestData);
        } else {
            // Fix magento bug with $associatedProducts returning false instead of array
            $requestData['super_group'] = 1;
        }

        return $request;
    }

    /**
     * Retrieve grouped item options
     *
     * @param ProductOptionInterface $productOption
     * @return array
     */
    private function getGroupedItemOptions(ProductOptionInterface $productOption)
    {
        if ($productOption
            && $productOption->getExtensionAttributes()
            && $productOption->getExtensionAttributes()->getAwRlGroupedItemOptions()
        ) {
            return $productOption->getExtensionAttributes()
                ->getAwRlGroupedItemOptions();
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function convertToProductOption(DataObject $request)
    {
        $superGroup = $request->getSuperGroup();
        if (!empty($superGroup) && is_array($superGroup)) {
            $data = [];
            foreach ($superGroup as $optionId => $optionValue) {
                /** @var GroupedItemOptionValueInterface $option */
                $option = $this->groupedItemOptionValueFactory->create();
                $option->setOptionId($optionId);
                $option->setOptionValue($optionValue);
                $data[] = $option;
            }

            return ['aw_rl_grouped_item_options' => $data];
        }

        return [];
    }
}
