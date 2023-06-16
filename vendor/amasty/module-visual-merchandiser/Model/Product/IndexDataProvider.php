<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Model\Product;

use Amasty\VisualMerch\Model\Rule as MerchRule;
use Magento\CatalogRule\Api\Data\RuleInterface;

class IndexDataProvider extends \Magento\Framework\Model\AbstractModel
{
    public const DEFAULT_PRODUCT = 0;

    /**
     * @var \Amasty\VisualMerch\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Sorting
     */
    private $sorting;

    /**
     * @var \Magento\Catalog\Api\Data\CategoryInterface
     */
    private $category;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $defaultStore;

    /**
     * @var \Amasty\VisualMerch\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Amasty\VisualMerch\Model\ResourceModel\Product
     */
    private $productPositionDataResource;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\VisualMerch\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Amasty\VisualMerch\Model\Product\Sorting $sorting,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\VisualMerch\Model\RuleFactory $ruleFactory,
        \Amasty\VisualMerch\Model\ResourceModel\Product $productPositionDataResource,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sorting = $sorting;
        $this->emulation = $emulation;
        $this->defaultStore = current($storeManager->getStores());
        $this->ruleFactory = $ruleFactory;
        $this->productPositionDataResource = $productPositionDataResource;
    }

    /**
     * @param $category
     * @return array
     */
    public function getProductPositionData($category, $storeId)
    {
        $this->setStoreId($storeId);
        $this->emulation->startEnvironmentEmulation($storeId);
        $this->initCategory($category);
        $ruleCollection = $this->getRuleCollection();
        $allIds = $ruleCollection->getProductIds();
        $allIds = $this->sortIds($allIds);
        $this->emulation->stopEnvironmentEmulation();
        return array_flip($allIds);
    }

    /**
     * @param $rule
     * @param $product
     * @param $storeId
     * @return bool
     */
    public function validateProductByRule($rule, $product, $storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId);
        $validationResult = $rule->validate($product);
        $this->emulation->stopEnvironmentEmulation();
        return !!$validationResult;
    }

    /**
     * @return \Amasty\VisualMerch\Model\ResourceModel\Product\Collection
     */
    private function getRuleCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $this->getAmastyRule()->applyAttributesFilter($collection);
        if ($this->isEmptyRule()) {
            $collection->addAttributeToFilter('entity_id', self::DEFAULT_PRODUCT);
        }
        $this->setCollectionOrder($collection);
        return $collection;
    }

    /**
     * @return bool
     */
    private function isEmptyRule()
    {
        $sqlConditions = $this->getAmastyRule()->getConditions()->collectConditionSql();
        return empty($sqlConditions);
    }

    /**
     * @param $collection
     * @return $this
     */
    private function setCollectionOrder($collection)
    {
        $this->sorting->applySorting($collection, $this->getCategory()->getData('amasty_category_product_sort'));
        return $this;
    }

    /**
     * @param $ids
     * @return array
     */
    private function sortIds($ids)
    {
        $sorted = $this->preparePositionDataForSort($ids);
        $ids = array_diff($ids, $sorted);
        $itemsCount = count($ids) + count($sorted);
        $idx = 0;
        while ($idx < $itemsCount) {
            if (!isset($sorted[$idx]) && current($ids)) {
                $sorted[$idx] = current($ids);
                next($ids);
            }
            $idx++;
        }

        ksort($sorted, SORT_NUMERIC);
        return $sorted;
    }

    /**
     * @param array $ids
     * @return array
     */
    private function preparePositionDataForSort($ids)
    {
        $positionData = $this->getCategory()->getProductPositionData() ?: [];
        $positionData = array_intersect(array_flip($positionData), $ids);
        $maxPosition = count($ids) - 1;
        foreach ($positionData as $position => $productId) {
            if ($position > $maxPosition) {
                $positionData[$maxPosition] = $productId;
                $maxPosition--;
            }
        }

        return $positionData;
    }

    /**
     * @param $category
     * @return $this
     * @throws \Exception
     */
    protected function initCategory($category)
    {
        if (!$category->getId()) {
            // @codingStandardsIgnoreLine
            throw new \Exception(__('Requested category does not exist'));
        }

        if (!$category->getAmastyRule()) {
            $rule = $this->ruleFactory->create();
            $conditions = $category->getData('amasty_dynamic_conditions');
            $category->setData('amasty_rule', $rule->setConditionsSerialized($conditions));
        }

        $this->productPositionDataResource->loadProductPositionData($category);
        $this->category = $category;

        return $this;
    }

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        if (!$this->hasData('store_id')) {
            $this->setData('store_id', $this->defaultStore->getId());
        }

        return $this->getData('store_id');
    }

    /**
     * @return MerchRule
     */
    private function getAmastyRule(): RuleInterface
    {
        return $this->getCategory()->getAmastyRule();
    }
}
