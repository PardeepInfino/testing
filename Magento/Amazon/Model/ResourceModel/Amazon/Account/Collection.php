<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Account;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\Amazon\Account::class,
            \Magento\Amazon\Model\ResourceModel\Amazon\Account::class
        );
    }
}
