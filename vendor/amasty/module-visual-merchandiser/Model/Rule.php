<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Model;

use Amasty\Base\Model\Serializer;
use Amasty\VisualMerch\Model\ResourceModel\Product\Collection;
use Amasty\VisualMerch\Model\Rule\Condition\Combine as MerchConditions;
use Amasty\VisualMerch\Model\Rule\Condition\Optimization\ConditionsOptimizerInterface;

class Rule extends \Magento\CatalogRule\Model\Rule
{
    /**
     * @var Serializer|null
     */
    protected $serializer;

    /**
     * @var ConditionsOptimizerInterface|null
     */
    private $conditionsOptimizer;

    protected function _construct()
    {
        $amastySerializer = $this->getData('amastySerializer');

        if ($amastySerializer) {
            $this->serializer = $amastySerializer;
        }

        $conditionsOptimizer = $this->getData('conditionsOptimizer');

        if ($conditionsOptimizer instanceof ConditionsOptimizerInterface) {
            $this->conditionsOptimizer = $conditionsOptimizer;
        }

        parent::_construct();
    }

    /**
     * @param Collection $productCollection
     * @return $this
     */
    public function applyAttributesFilter(Collection $productCollection)
    {
        $conditions = $this->getConditions();

        if ($this->conditionsOptimizer !== null) {
            $this->conditionsOptimizer->optimize($conditions);
        }

        if ($conditions instanceof MerchConditions) {
            $this->setAggregator($conditions->getAggregator());
            $conditions->collectValidatedAttributes($productCollection);
            $condition = $conditions->collectConditionSql();

            if (!empty($condition)) {
                $productCollection->getSelect()->where($condition);
            }
            $productCollection->getSelect()->group('e.entity_id');
        }

        return $this;
    }
}
