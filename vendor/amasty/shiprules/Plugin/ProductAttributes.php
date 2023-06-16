<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Plugin;

/**
 * Class ProductAttributes
 * phpcs:ignoreFile
 */
class ProductAttributes extends \Amasty\CommonRules\Plugin\ProductAttributes
{
    /**
     * ProductAttributes constructor.
     * @param \Amasty\Shiprules\Model\ResourceModel\Rule $resourceTable
     */
    public function __construct(\Amasty\Shiprules\Model\ResourceModel\Rule $resourceTable)
    {
        parent::__construct($resourceTable);
    }
}
