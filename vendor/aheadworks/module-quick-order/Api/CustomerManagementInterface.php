<?php
namespace Aheadworks\QuickOrder\Api;

/**
 * Interface CustomerManagementInterface
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * Check if quick order is active for customer group
     *
     * @param int $customerGroupId
     * @param int|null $websiteId
     * @return bool
     */
    public function isActiveForCustomerGroup($customerGroupId, $websiteId = null);
}
