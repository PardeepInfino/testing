<?php
namespace Aheadworks\QuickOrder\Model\Service;

use Aheadworks\QuickOrder\Api\CustomerManagementInterface;
use Aheadworks\QuickOrder\Model\Config;

/**
 * Class CustomerService
 *
 * @package Aheadworks\QuickOrder\Model\Service
 */
class CustomerService implements CustomerManagementInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function isActiveForCustomerGroup($customerGroupId, $websiteId = null)
    {
        return $this->config->isEnabled($websiteId)
            && $this->config->isEnabledForCustomerGroup($customerGroupId, $websiteId);
    }
}
