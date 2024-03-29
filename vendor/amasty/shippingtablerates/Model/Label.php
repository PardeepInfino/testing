<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shipping Table Rates for Magento 2
 */

namespace Amasty\ShippingTableRates\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Label Data of Shipping Method.
 *  Shipping Method can have label for each store scope
 */
class Label extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Amasty\ShippingTableRates\Model\ResourceModel\Label::class);
    }

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Model\Context $context
    ) {
        parent::__construct($context, $coreRegistry);
    }
}
