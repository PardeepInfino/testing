<?php
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options;

use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;

/**
 * Class Mapper
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options
 */
class Mapper
{
    const DEFAULT_QTY = 1;

    /**
     * Request params mapping
     *
     * @var array
     */
    private $map = [
        'list_id' => RequisitionListItemInterface::LIST_ID,
        'product' => RequisitionListItemInterface::PRODUCT_ID,
        'product_id' => RequisitionListItemInterface::PRODUCT_ID,
        'qty' => RequisitionListItemInterface::PRODUCT_QTY
    ];

    /**
     * @param array $mapData
     */
    public function __construct(
        $mapData = []
    ) {
        $this->map = array_merge($this->map, $mapData);
    }

    /**
     * Resolve params with mappings
     *
     * @param array $params
     * @return array
     */
    public function mapParams($params)
    {
        $result = [];
        foreach ($params as $key => $param) {
            if (isset($this->map[$key])) {
                $result[$this->map[$key]] = $param;
            }
        }

        if (!isset($result[RequisitionListItemInterface::PRODUCT_QTY])) {
            $result[RequisitionListItemInterface::PRODUCT_QTY] = self::DEFAULT_QTY;
        }

        return $result;
    }
}
