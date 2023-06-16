<?php
namespace Aheadworks\RequisitionLists\Model\Service;

use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;
use Aheadworks\RequisitionLists\Model\Config;

/**
 * Class CustomerService
 */
class CustomerService implements CustomerManagementInterface
{
    /**
     * @var Config
     */
    private $config;


    /**
     * @param Config $config
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function isActiveForCurrentWebsite($websiteId = null)
    {
        return $this->config->isEnabled($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isActiveForCurrentWebsiteByName($name, $websiteId = null)
    {
        return $this->config->getGeneralConfigByName($name, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isShowInOrderPageForCurrentWebsite($websiteId = null)
    {
        return $this->config->isShowInOrderPage($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isShowInCatalogForCurrentWebsite($websiteId = null)
    {
        return $this->config->isShowInCatalog($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isShowInCartPageForCurrentWebsite($websiteId = null)
    {
        return $this->config->isShowInCartPage($websiteId);
    }
}
