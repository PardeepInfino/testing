<?php
namespace Aheadworks\RequisitionLists\Model\Product\Option\Configurable;

use Magento\ConfigurableProduct\Api\Data\ConfigurableItemOptionValueInterface;
use Magento\Framework\DataObject;
use Magento\ConfigurableProduct\Model\ProductOptionProcessor;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Processor
 * @package Aheadworks\RequisitionLists\Model\Product\Option\Configurable
 */
class Processor extends ProductOptionProcessor
{
    /**
     * {@inheritdoc}
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $options = $this->getConfigurableItemOptions($productOption);
        if (!empty($options)) {
            $requestData = [];
            foreach ($options as $option) {
                /** @var ConfigurableItemOptionValueInterface $option */
                $requestData['super_attribute'][$option->getOptionId()] = (string)$option->getOptionValue();
            }
            $request->addData($requestData);
        }

        return $request;
    }
}
