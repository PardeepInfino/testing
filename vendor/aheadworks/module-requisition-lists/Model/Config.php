<?php
namespace Aheadworks\RequisitionLists\Model;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail;
use Magento\CatalogInventory\Helper\Minsaleqty;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\ConfigurableProduct\Model\Product\Configuration\Item\ItemProductResolver;
use Magento\CatalogInventory\Model\Configuration;

/**
 * Class Config
 * @package Aheadworks\RequisitionLists\Model
 */
class Config
{
    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL = 'aw_requisition_lists/general/';
    const XML_PATH_GENERAL_ENABLED = 'aw_requisition_lists/general/enabled';
    const XML_PATH_GENERAL_SHOW_IN_CART_PAGE = 'aw_requisition_lists/general/show_in_cart_page';
    const XML_PATH_GENERAL_SHOW_IN_ORDER_PAGE = 'aw_requisition_lists/general/show_in_order_page';
    const XML_PATH_GENERAL_SHOW_IN_CATALOG = 'aw_requisition_lists/general/show_in_catalog';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Minsaleqty
     */
    private $minSaleQty;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Minsaleqty $minSaleQty
     * @param Context $context
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Minsaleqty $minSaleQty,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->minSaleQty = $minSaleQty;
        $this->context = $context;
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
     * @param int|null $websiteId
     * @param string $name
     * @return bool
     */
    public function getGeneralConfigByName($name, $websiteId) {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL . $name,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if module is show in cart page
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isShowInCartPage($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SHOW_IN_CART_PAGE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if module is show in catalog
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isShowInCatalog($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SHOW_IN_CATALOG,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if module is show in order page
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isShowInOrderPage($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_SHOW_IN_ORDER_PAGE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }


    /**
     * Is use parent image for configurable items
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isUseParentImageForConfigurable($storeId = null)
    {
        return $this->scopeConfig->getValue(
            ItemProductResolver::CONFIG_THUMBNAIL_SOURCE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) == Thumbnail::OPTION_USE_PARENT_IMAGE;
    }

    /**
     * Check if out of stock products are displayed
     *
     * @return bool
     */
    public function isConfiguredToShowOutOfStockProducts()
    {
        return $this->scopeConfig->isSetFlag(
            Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get minimal sale qty for product
     */
    public function getMinSalableQty()
    {
        return $this->minSaleQty->getConfigValue($this->context->getValue(CustomerContext::CONTEXT_GROUP)) ?? 1;
    }
}
