<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Visual Merchandiser for Magento 2
 */

namespace Amasty\VisualMerch\Plugin\Catalog\Model\Indexer\Category;

use Magento\Catalog\Model\Indexer\Category\Product as CategoryProductIndexer;

/**
 * Class Product
 *
 * Plugin for category product indexer
 */
class Product
{
    /**
     * @var \Amasty\VisualMerch\Model\Product\IndexDataProvider
     */
    private $dataProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Amasty\VisualMerch\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    private $cacheContext;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var array
     */
    private $changedCategoryIds = [];

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    private $eavAttribute;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    public function __construct(
        \Amasty\VisualMerch\Model\Product\IndexDataProvider $dataProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Amasty\VisualMerch\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Indexer\CacheContext $cacheContext,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\State $appState
    ) {
        $this->dataProvider = $dataProvider;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->connection = $resource->getConnection();
        $this->ruleFactory = $ruleFactory;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->eavAttribute = $eavAttribute;
        $this->appState = $appState;
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    private function updateCategoryProducts($categoryIds = [])
    {
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('amlanding_is_dynamic', 1)
            ->addAttributeToSelect('amasty_category_product_sort')
            ->addAttributeToSelect('amasty_dynamic_conditions');
        $this->changedCategoryIds = $categoryCollection->getAllIds();
        if ($this->eavAttribute->getIdByCode('category', 'amlanding_page_id')) {
            $categoryCollection->addAttributeToFilter('amlanding_page_id', ['notnull' => true]);
        }

        if (!empty($categoryIds)) {
            $categoryCollection->addIdFilter($categoryIds);
        }

        $stores = $this->storeManager->getStores();
        foreach ($categoryCollection as $category) {
            $rows = [];
            $parentIds = $category->getParentIds();
            foreach ($stores as $store) {
                if (in_array($store->getRootCategoryId(), $parentIds)) {
                    $productIds = $this->dataProvider->getProductPositionData($category, $store->getId());
                    foreach ($productIds as $productId => $position) {
                        if (isset($rows[$productId])) {
                            continue;
                        }
                        $rows[$productId] = [
                            'entity_id' => null,
                            'category_id' => $category->getId(),
                            'product_id' => $productId,
                            'position' => $position
                        ];
                    }
                }
            }

            $table = $categoryCollection->getTable('catalog_category_product');
            $this->connection->delete(
                $table,
                ['category_id = (?)' => $category->getId()]
            );
            $this->changedCategoryIds[] = $category->getId();
            if (count($rows)) {
                $this->connection->insertOnDuplicate($table, $rows);
                $this->cacheContext->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, array_keys($rows));
                $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    private function checkCorrectAreaCode()
    {
        if ($this->appState->isAreaCodeEmulated()) {
            return $this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND;
        }
        return true;
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteFull(CategoryProductIndexer $indexer)
    {
        if ($this->checkCorrectAreaCode()) {
            $this->updateCategoryProducts();
        }
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @param $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecuteFull(CategoryProductIndexer $indexer, $result)
    {
        if (!empty($this->changedCategoryIds)) {
            $this->cacheContext->registerEntities(
                \Magento\Catalog\Model\Product::CACHE_TAG,
                $this->changedCategoryIds
            );
            $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
        }
        return $result;
    }
}
