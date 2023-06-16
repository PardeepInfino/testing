<?php
namespace Aheadworks\QuickOrder\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface GroupedItemOptionValueInterface
 * @api
 */
interface GroupedItemOptionValueInterface extends ExtensibleDataInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const OPTION_ID = 'option_id';
    const OPTION_VALUE = 'option_value';

    /**#@-*/

    /**
     * Get option ID
     *
     * @return string
     */
    public function getOptionId();

    /**
     * Set option ID
     *
     * @param string $value
     * @return void
     */
    public function setOptionId($value);

    /**
     * Get option value
     *
     * @return int|null
     */
    public function getOptionValue();

    /**
     * Set option value
     *
     * @param int|null $value
     * @return void
     */
    public function setOptionValue($value);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\QuickOrder\Api\Data\GroupedItemOptionValueExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set extension attributes object
     *
     * @param \Aheadworks\QuickOrder\Api\Data\GroupedItemOptionValueExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\GroupedItemOptionValueExtensionInterface $extensionAttributes
    );
}
