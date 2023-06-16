<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model\Rule\Factory;

/**
 * Class HandlerFactoryAbstract
 */
abstract class HandlerFactoryAbstract implements HandleFactoryInterface
{
    /**
     * @var array
     */
    protected $handlers;

    /**
     * @param string $type
     * @return array
     */
    public function create($type = self::CUSTOMER_HANDLE)
    {
        return $this->getConditionsByType($type);
    }

    /**
     * @param $type
     * @return bool|mixed
     */
    public function getHandlerByType($type)
    {
        return isset($this->handlers[$type]) ? $this->handlers[$type] : false;
    }

    /**
     * @param $type
     * @return array
     */
    abstract protected function getConditionsByType($type);
}
