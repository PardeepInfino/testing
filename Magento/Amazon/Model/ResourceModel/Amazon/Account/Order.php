<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Account;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Order
 */
class Order extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_account_order',
            'id'
        );
    }
}