<?php
namespace Aheadworks\QuickOrder\Model\Product\Option\CustomOptions;

use Aheadworks\QuickOrder\Api\Data\CustomOptionValueInterface;
use Magento\Catalog\Model\Webapi\Product\Option\Type\File\Processor as FileProcessor;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class CustomOptionValue
 */
class CustomOptionValue extends AbstractExtensibleModel implements CustomOptionValueInterface
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        FileProcessor $fileProcessor,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory,
            $resource, $resourceCollection, $data);
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @inheritDoc
     */
    public function getOptionValue()
    {
        $value =  $this->getData(self::OPTION_VALUE);
        if ($value == 'file') {
            /** @var \Magento\Framework\Api\Data\ImageContentInterface $fileInfo */
            $imageContent = $this->getExtensionAttributes()
                ? $this->getExtensionAttributes()->getFileInfo()
                : null;
            if ($imageContent) {
                $value = $this->fileProcessor->processFileContent($imageContent);
            }
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOptionId($value)
    {
        return $this->setData(self::OPTION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOptionValue($value)
    {
        return $this->setData(self::OPTION_VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\CustomOptionValueExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function getMonth()
    {
        return $this->getData(self::MONTH);
    }

    /**
     * @inheritDoc
     */
    public function setMonth($value)
    {
        return $this->setData(self::MONTH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDay()
    {
        return $this->getData(self::DAY);
    }

    /**
     * @inheritDoc
     */
    public function setDay($value)
    {
        return $this->setData(self::DAY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getYear()
    {
        return $this->getData(self::YEAR);
    }

    /**
     * @inheritDoc
     */
    public function setYear($value)
    {
        return $this->setData(self::YEAR, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHour()
    {
        return $this->getData(self::HOUR);
    }

    /**
     * @inheritDoc
     */
    public function setHour($value)
    {
        return $this->setData(self::HOUR, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMinute()
    {
        return $this->getData(self::MINUTE);
    }

    /**
     * @inheritDoc
     */
    public function setMinute($value)
    {
        return $this->setData(self::MINUTE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDayPart()
    {
        return $this->getData(self::DAY_PART);
    }

    /**
     * @inheritDoc
     */
    public function setDayPart($value)
    {
        return $this->setData(self::DAY_PART, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsDate()
    {
        return $this->getData(self::IS_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setIsDate($value)
    {
        return $this->setData(self::IS_DATE, $value);
    }
}