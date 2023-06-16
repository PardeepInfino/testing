<?php
namespace Aheadworks\QuickOrder\Api\Data;

interface CustomOptionValueInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants
     */
    const OPTION_ID = 'option_id';
    const OPTION_VALUE = 'option_value';
    const MONTH = 'month';
    const DAY = 'day';
    const YEAR = 'year';
    const HOUR = 'hour';
    const MINUTE = 'minute';
    const DAY_PART = 'day_part';
    const IS_DATE = 'is_date';
    /**#@-*/

    /**
     * Get option id
     *
     * @return string
     */
    public function getOptionId();

    /**
     * Set option id
     *
     * @param string $value
     * @return bool
     */
    public function setOptionId($value);

    /**
     * Get month
     *
     * @return string
     */
    public function getMonth();

    /**
     * Set month
     *
     * @param string $value
     * @return bool
     */
    public function setMonth($value);

    /**
     * Get day
     *
     * @return string
     */
    public function getDay();

    /**
     * Set day
     *
     * @param string $value
     * @return bool
     */
    public function setDay($value);

    /**
     * Get year
     *
     * @return string
     */
    public function getYear();

    /**
     * Set year
     *
     * @param string $value
     * @return bool
     */
    public function setYear($value);

    /**
     * Get hour
     *
     * @return string
     */
    public function getHour();

    /**
     * Set hour
     *
     * @param string $value
     * @return bool
     */
    public function setHour($value);

    /**
     * Get minute
     *
     * @return string
     */
    public function getMinute();

    /**
     * Set minute
     *
     * @param string $value
     * @return bool
     */
    public function setMinute($value);

    /**
     * Get day part
     *
     * @return string
     */
    public function getDayPart();

    /**
     * Set day part
     *
     * @param string $value
     * @return bool
     */
    public function setDayPart($value);

    /**
     * Get is date
     *
     * @return string
     */
    public function getIsDate();

    /**
     * Set is date
     *
     * @param string $value
     * @return bool
     */
    public function setIsDate($value);

    /**
     * Get option value
     *
     * @return string
     */
    public function getOptionValue();

    /**
     * Set option value
     *
     * @param string $value
     * @return bool
     */
    public function setOptionValue($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Aheadworks\QuickOrder\Api\Data\CustomOptionValueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Aheadworks\QuickOrder\Api\Data\CustomOptionValueExtensionInterface|null $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\CustomOptionValueExtensionInterface $extensionAttributes
    );
}