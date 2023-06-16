<?php
namespace Aheadworks\QuickOrder\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Customer\Model\GroupManagement;

/**
 * Class CustomerGroupList
 *
 * @package Aheadworks\QuickOrder\Model\System\Config\Backend
 */
class CustomerGroupList extends ConfigValue
{
    /**
     * Remove other options if All Groups option is included
     *
     * @throws \Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && in_array(GroupManagement::CUST_GROUP_ALL, $value)) {
            $value = [GroupManagement::CUST_GROUP_ALL];
            $this->setValue($value);
        }

        return parent::beforeSave();
    }
}
