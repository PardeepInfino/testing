<?php
namespace Aheadworks\QuickOrder\Model\Product\Option\CustomOptions;

use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\CustomOptions\CustomOption;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Catalog\Model\ProductOptionProcessor as NativeProductOptionProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class ProductOptionProcessor
 */
class ProductOptionProcessor extends NativeProductOptionProcessor
{
    /**
     * @param DataObjectFactory $objectFactory
     * @param CustomOptionFactory $customOptionFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        CustomOptionFactory $customOptionFactory
    ) {
        parent::__construct($objectFactory, $customOptionFactory);
    }

    /**
     * Retrieve custom options
     *
     * @param ProductOptionInterface $productOption
     * @return array
     */
    protected function getCustomOptions(ProductOptionInterface $productOption)
    {
        if ($productOption
            && $productOption->getExtensionAttributes()
            && $productOption->getExtensionAttributes()->getAwQoCustomOptions()
        ) {
            $awQoCustomOptions = $productOption->getExtensionAttributes()
                ->getAwQoCustomOptions();

            foreach ($awQoCustomOptions as $option) {
                if ($option->getIsDate() && !$option->getOptionValue()) {
                    $option->setOptionValue($option->getData());
                }
            }

            return $awQoCustomOptions;
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function convertToProductOption(DataObject $request)
    {
        $options = $request->getOptions();
        if (!empty($options) && is_array($options)) {
            $data = [];
            /** @var CustomOption $option */
            foreach ($options as $optionId => $optionValue) {
                $option = $this->customOptionFactory->create();

                if (is_array($optionValue) && !$this->isDate($optionValue)) {
                    $optionValue = implode(',', $optionValue);
                } elseif(is_array($optionValue) && $this->isDate($optionValue)) {
                    foreach ($optionValue as $key => $value) {
                        $option->setData($key, $value);
                    }

                    $option->setIsDate(true);
                    $optionValue = null;
                }

                /** @var CustomOption $option */
                $option->setOptionId($optionId)->setOptionValue($optionValue);
                $data[] = $option;
            }

            return ['aw_qo_custom_options' => $data];
        }

        return [];
    }

    /**
     * Check if the option has a date
     *
     * @param array $optionValue
     * @return bool
     */
    private function isDate(array $optionValue): bool
    {
        $hasDate = !empty($optionValue['day'])
            && !empty($optionValue['month'])
            && !empty($optionValue['year']);

        $hasTime = !empty($optionValue['hour'])
            && isset($optionValue['minute']);

        return $hasDate || $hasTime || !empty($optionValue['date']);
    }
}