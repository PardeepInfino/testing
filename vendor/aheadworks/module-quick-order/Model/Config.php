<?php
namespace Aheadworks\QuickOrder\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\GroupManagement;

/**
 * Class Config
 *
 * @package Aheadworks\QuickOrder\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_ENABLED = 'aw_quick_order/general/enabled';
    const XML_PATH_GENERAL_IS_ADD_TO_LIST_BUTTON_DISPLAYED = 'aw_quick_order/general/is_add_to_list_button_displayed';
    const XML_PATH_GENERAL_IS_QTY_INPUT_DISPLAYED = 'aw_quick_order/general/is_qty_input_displayed';
    const XML_PATH_GENERAL_CUSTOMER_GROUP_LIST_TO_BE_ENABLED
        = 'aw_quick_order/general/customer_group_list_to_be_enabled';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabled($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if enabled for customer group
     *
     * @param int $customerGroupId
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabledForCustomerGroup($customerGroupId, $websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_CUSTOMER_GROUP_LIST_TO_BE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        $customerGroups = explode(',', (string)$value);
        return $value !== null
            && (in_array(GroupManagement::CUST_GROUP_ALL, $customerGroups)
            || in_array($customerGroupId, $customerGroups));
    }

    /**
     * Check if add to list button is displayed
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isAddToListButtonDisplayed($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_ADD_TO_LIST_BUTTON_DISPLAYED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if quantity input is displayed
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isQtyInputDisplayed($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_IS_QTY_INPUT_DISPLAYED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }
}
