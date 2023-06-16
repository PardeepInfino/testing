<?php
namespace Aheadworks\RequisitionLists\Model\Product\Option\Bundle;

use Magento\Framework\DataObject;
use Magento\Bundle\Api\Data\BundleOptionInterface;
use Magento\Bundle\Model\ProductOptionProcessor;
use Magento\Catalog\Api\Data\ProductOptionInterface;

/**
 * Class Processor
 * @package Aheadworks\RequisitionLists\Model\Product\Option\Bundle
 */
class Processor extends ProductOptionProcessor
{
    /**
     * @inheritdoc
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $bundleOptions = $this->getBundleOptions($productOption);
        if (!empty($bundleOptions) && is_array($bundleOptions)) {
            $requestData = [];
            /** @var BundleOptionInterface $option */
            foreach ($bundleOptions as $option) {
                $requestData['bundle_option'][$option->getOptionId()] = $option->getOptionSelections();
                $requestData['bundle_option_qty'][$option->getOptionId()] = $option->getOptionQty();
            }
            $request->addData($requestData);
        }

        return $request;
    }
}
