<?php

/**
 * Product:       Xtento_StockImport
 * ID:            %!uniqueid!%
 * Last Modified: 2022-10-11T19:19:33+00:00
 * File:          Model/Import/Entity/Stock.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\StockImport\Model\Import\Entity;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Customer\Model\Group;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Xtento\StockImport\Helper\Entity;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\AttributeFactory;
use \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Xtento\StockImport\Helper\Module;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\PageCache\Cache;
use Magento\CatalogInventory\Model\ResourceModel\Stock as ResourceStock;

class Stock extends AbstractEntity
{
    static $importStock = true;
    static $importPrices = true;
    static $importSpecialPrices = true;
    static $importCost = true;
    static $importProductStatus = true;
    static $importCustomAttributes = true;
    static $maxImportFilterCount = 3;

    /*
     * Attribute to identify stock items by
     */
    protected $attributeToLoadBy = 'sku';
    /*
     * Product identifiers (could be the SKU, an attribute, ... - attribute loaded by defined in function getProductIdsForProductIdentifiers)
     */
    protected $productIdentifiers = [];
    /*
     * Associative array holding productIdentifer => product_id
     */
    protected $productMap = [];
    protected $productTypeMap = [];
    protected $productIdToSku = []; // Required for MSI
    /*
     * Products not found in Magento
     */
    protected $productsNotFound = [];
    /*
     * Existing stock_items taken directly from the cataloginventory_stock_item table.
     */
    protected $stockItems = [];
    /*
     * Existing MSI stock items
     */
    protected $msiItems = [];
    /*
     * Existing stock_status items taken directly from the cataloginventory_stock_status table.
     */
    protected $stockStatusItems = [];
    /*
     * Which stock_items have been modified? Important for re-index
     */
    protected $modifiedStockItems = [];
    /*
     * Which MSI items have been modified?
     */
    protected $modifiedMsiItems = [];
    /*
     * Current prices for products
     */
    protected $prices = [];
    protected $specialPrices = [];
    protected $costValues = [];
    /*
     * Updated prices
     */
    protected $updatedPrices = [];
    protected $updatedSpecialPrices = [];
    protected $updatedCostValues = [];
    protected $updatedTierPrices = [];
    /*
     * Current product status
     */
    protected $productStatus = [];
    protected $updatedProductStatuses = [];
    /*
     * Custom product attribute values
     */
    protected $customProductAttributeValues = [];
    protected $updatedCustomProductAttributes = [];
    protected $customProductAttributes = [];

    /**
     * Entity ID field for catalog_product_entity_decimal update; changed in EE 2.1
     */
    protected $productEntityDecimalFieldName = 'entity_id';

    /**
     * Price scope, per website or global?
     */
    protected $priceScope = null; // 0 = global, 1 = website

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Entity
     */
    protected $entityHelper;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var AttributeFactory
     */
    protected $entityAttributeFactory;

    /**
     * @var AttributeCollectionFactory
     */
    protected $entityAttributeCollectionFactory;

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * @var Action
     */
    protected $productAction;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Configurable
     */
    protected $configurableProduct;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Cache
     */
    protected $pageCache;

    /**
     * @var ResourceStock
     */
    protected $resourceStock;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Xtento\XtCore\Helper\Utils
     */
    protected $utilsHelper;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Stock constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param Registry $frameworkRegistry
     * @param ObjectManagerInterface $objectManager
     * @param ManagerInterface $eventManagerInterface
     * @param ProductFactory $productFactory
     * @param Entity $entityHelper
     * @param Config $eavConfig
     * @param AttributeFactory $entityAttributeFactory
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param Module $moduleHelper
     * @param Action $productAction
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Configurable $configurableProduct
     * @param StockRegistryInterface $stockRegistry
     * @param IndexerRegistry $indexerRegistry
     * @param TypeListInterface $typeList
     * @param Cache $pageCache
     * @param ResourceStock $resourceStock
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \Magento\Framework\App\CacheInterface $cacheManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Registry $frameworkRegistry,
        ObjectManagerInterface $objectManager,
        ManagerInterface $eventManagerInterface,
        ProductFactory $productFactory,
        Entity $entityHelper,
        Config $eavConfig,
        AttributeFactory $entityAttributeFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        Module $moduleHelper,
        Action $productAction,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Configurable $configurableProduct,
        StockRegistryInterface $stockRegistry,
        IndexerRegistry $indexerRegistry,
        TypeListInterface $typeList,
        Cache $pageCache,
        ResourceStock $resourceStock,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->eventManager = $eventManagerInterface;
        $this->objectManager = $objectManager;
        $this->productFactory = $productFactory;
        $this->entityHelper = $entityHelper;
        $this->eavConfig = $eavConfig;
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->entityAttributeCollectionFactory = $attributeCollectionFactory;
        $this->moduleHelper = $moduleHelper;
        $this->productAction = $productAction;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->configurableProduct = $configurableProduct;
        $this->stockRegistry = $stockRegistry;
        $this->indexerRegistry = $indexerRegistry;
        $this->cacheTypeList = $typeList;
        $this->pageCache = $pageCache;
        $this->resourceStock = $resourceStock;
        $this->productMetadata = $productMetadata;
        $this->utilsHelper = $utilsHelper;
        $this->cacheManager = $cacheManager;
        $this->productRepository = $productRepository;
        parent::__construct($resourceConnection, $frameworkRegistry, $data);
    }

    /**
     * Prepare import by getting a mapping of the attribute used to identify the product and its product id
     *
     * @param $updatesInFilesToProcess
     *
     * @return bool
     */
    public function prepareImport($updatesInFilesToProcess)
    {
        // Check is Magento EE >=2.1, if so use different catalog_product_entity_decimal price field name (row_id in EE 2.1)
        if ($this->utilsHelper->isMagentoEnterprise() && $this->utilsHelper->mageVersionCompare($this->productMetadata->getVersion(), '2.1.0', '>=')) {
            $this->productEntityDecimalFieldName = 'row_id';
        }

        // Prepare import
        $this->eventManager->dispatch('xtento_stockimport_stockupdate_before', [
            'profile' => $this->getProfile(),
            'log' => $this->getLogEntry(),
            'updates' => &$updatesInFilesToProcess
        ]);

        if (!$this->getTestMode()) {
            // Reset stock, uncomment to enable
            /*$this->writeAdapter->update(
                $this->getTableName('cataloginventory_stock_item'),
                array('qty' => 0, 'is_in_stock' => 0)
            );
            $this->writeAdapter->update(
                $this->getTableName('cataloginventory_stock_status'),
                array('qty' => 0, 'stock_status' => 0)
            );*/

            // Reset stock for specific product IDs, uncomment to enable
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            /*$productCollection = $this->productFactory->create()->getCollection();
            $productCollection->addAttributeToFilter('supplier', 'techdata');
            $productIds = $productCollection->getAllIds();
            if (is_array($productIds)) {
                $this->writeAdapter->update(
                    $this->getTableName('cataloginventory_stock_item'),
                    array('qty' => 0, 'is_in_stock' => 0),
                    "product_id IN (" . join(",", $productIds) . ")"
                );
                $this->writeAdapter->update(
                    $this->getTableName('cataloginventory_stock_status'),
                    array('qty' => 0, 'stock_status' => 0),
                    "product_id IN (" . join(",", $productIds) . ")"
                );
            }*/
        }

        // When importing duplicate SKUs from multiple files, the import may fail, as it thinks it needs to insert the stock item/status entry again. Refresh stock item/status tables after every file processed.
        // Prepare product identifiers
        $this->getProductIdentifiers($updatesInFilesToProcess);
        if (empty($this->productIdentifiers)) {
            $this->getLogEntry()->addDebugMessage(__('No products could be found in the import file.'));
            return false;
        }

        // Get product IDs for product identifiers
        $this->getProductIdsForProductIdentifiers();
        if (empty($this->productMap)) {
            $this->getLogEntry()->addDebugMessage(__('The products supplied in the import file could not be found in the Magento catalog.'));
            return false;
        }

        $this->applyFiltersToFoundProducts();
        if (empty($this->productMap)) {
            $this->getLogEntry()->addDebugMessage(__('The products supplied in the import file could not be found in the Magento catalog OR all were filtered by profile filters.'));
            return false;
        }

        // Find out which products couldn't be found in Magento
        foreach ($this->productIdentifiers as $productIdentifier) {
            if (!isset($this->productMap[$productIdentifier])) {
                array_push($this->productsNotFound, $productIdentifier);
            }
        }

        if (!$this->getTestMode()) {
            // Set all products not in import files to out of stock
            if ($this->getConfigFlag('reset_stock_of_products_not_in_file')) {
                // Get all products where stock is managed
                $select = $this->readAdapter->select()
                    ->from($this->getTableName('cataloginventory_stock_item'), ['product_id'])
                    ->where("manage_stock=1");
                $stockManagedProducts = $this->readAdapter->fetchCol($select);

                $productsToReset = [];
                foreach ($stockManagedProducts as $key => $productId) {
                    if (!in_array($productId, $this->productMap)) { // Product is not in import file, reset it
                        $productsToReset[] = $productId;
                    }
                }

                if (!empty($productsToReset)) {
                    $this->writeAdapter->update(
                        $this->getTableName('cataloginventory_stock_item'),
                        ['qty' => 0, 'is_in_stock' => 0],
                        "product_id IN (" . join(",", $productsToReset) . ")"
                    );
                    $this->writeAdapter->update(
                        $this->getTableName('cataloginventory_stock_status'),
                        ['qty' => 0, 'stock_status' => 0],
                        "product_id IN (" . join(",", $productsToReset) . ")"
                    );
                }

                if ($this->entityHelper->getMagentoMSISupport()) {
                    // Magento MSI version
                    // Not yet adjusted for "manage_stock"
                    $msiSource = false;
                    foreach ($updatesInFilesToProcess as $updateFile) {
                        foreach ($updateFile['ITEMS'] as $stockId => $updatesInFile) {
                            foreach ($updatesInFile as $productIdentifier => $updateData) {
                                if (isset($updateData['source_code'])) {
                                    $msiSource = $updateData['source_code'];
                                    break 3;
                                }
                            }
                        }
                    }
                    if ($msiSource !== false && !empty($this->productIdToSku)) {
                        $this->writeAdapter->update(
                            $this->getTableName('inventory_source_item'), [
                            'quantity' => 0,
                            'status' => 0
                        ], "sku not in ('" . join('\',\'', array_values($this->productIdToSku)) . "') and source_code=".$this->writeAdapter->quote($msiSource).""
                        );
                    }
                }
            }
        }

        // Which fields are in the file and should be handled for the import?
        $fieldsFound = [];
        foreach ($updatesInFilesToProcess as $updatesInFile) {
            if (isset($updatesInFile['FIELDS'])) {
                foreach ($updatesInFile['FIELDS'] as $field) {
                    $fieldsFound[] = $field;
                }
            }
        }
        if (!in_array('qty', $fieldsFound)
            && !in_array('is_in_stock', $fieldsFound)
            && !in_array('backorders', $fieldsFound)
            && !in_array('manage_stock', $fieldsFound)
            && !in_array('use_config_manage_stock', $fieldsFound)
            && !in_array('notify_stock_qty', $fieldsFound)
            && !in_array('min_qty', $fieldsFound)
            && !in_array('min_sale_qty', $fieldsFound)
            && !in_array('max_sale_qty', $fieldsFound)
        ) {
            self::$importStock = false;
        } else {
            self::$importStock = true;
        }
        if (!in_array('price', $fieldsFound)) {
            self::$importPrices = false;
        } else {
            self::$importPrices = true;
        }
        if (!in_array('special_price', $fieldsFound)) {
            self::$importSpecialPrices = false;
        } else {
            self::$importSpecialPrices = true;
        }
        if (!in_array('cost', $fieldsFound)) {
            self::$importCost = false;
        } else {
            self::$importCost = true;
        }
        if (!in_array('status', $fieldsFound)) {
            self::$importProductStatus = false;
        } else {
            self::$importProductStatus = true;
        }
        // Custom product attributes
        $customProductAttributeFound = false;
        foreach ($fieldsFound as $fieldFound) {
            if (stristr($fieldFound, 'cpa_') !== false) {
                $customProductAttributeFound = true;
            }
        }
        if ($customProductAttributeFound) {
            self::$importCustomAttributes = true;
        } else {
            self::$importCustomAttributes = false;
        }

        // Proceed with gathering information required for the import
        if (self::$importStock) {
            // Get current stock info - so what exists in the stock tables and what doesn't
            $this->getCurrentStockInfo();
            if ($this->entityHelper->getMagentoMSISupport()) {
                $this->getMsiItemInfo();
            }
        }

        if (self::$importPrices || self::$importSpecialPrices || self::$importCost) {
            // If price import is enabled.. get price info
            $this->priceScope = (int)$this->scopeConfig->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE) ?: 0;
            $this->getLogEntry()->addDebugMessage(__('Price scope for price updates: %1', $this->priceScope === 0 ? __('Global') : __('Per Website')));
            $this->getCurrentPriceInfo();
        }
        if (self::$importProductStatus) {
            $this->getCurrentProductInfo();
        }
        if (self::$importCustomAttributes) {
            $this->getCurrentProductAttributeValues($fieldsFound);
        }

        // Start transaction for all the updates.. performance is the key here!
        #$this->writeAdapter->query('LOCK TABLES '.$this->getTableName('cataloginventory_stock_status').' WRITE');
        #$this->writeAdapter->query('LOCK TABLES '.$this->getTableName('cataloginventory_stock_item').' WRITE');

        // Only start a transaction if no product data is updated:
        if (!self::$importPrices && !self::$importSpecialPrices && !self::$importCost && !self::$importProductStatus && !self::$importCustomAttributes) {
            $this->writeAdapter->beginTransaction();
        }

        $this->getLogEntry()->addDebugMessage(__('Transaction started. Starting import.'));
        return true;
    }

    /*
     * Get all the product identifiers we're supposed to identify stock items by. Could be the SKU or an attribute.
     */
    protected function getProductIdentifiers($updatesInFilesToProcess)
    {
        $this->productIdentifiers = [];
        foreach ($updatesInFilesToProcess as $updateFile) {
            foreach ($updateFile['ITEMS'] as $stockId => $updatesInFile) {
                foreach ($updatesInFile as $productIdentifier => $updateData) {
                    $productIdentifier = trim($productIdentifier);
                    array_push($this->productIdentifiers, strtolower($productIdentifier));
                }
            }
        }
        return $this->productIdentifiers;
    }

    /*
     * Get product ids for stock items based on the product identifiers supplied
     */
    protected function getProductIdsForProductIdentifiers()
    {
        // Which attribute is supposed be the identifier in the import file for the mapping to the actual products in Magento?
        if ($this->getConfig('product_identifier') == 'sku') {
            $this->attributeToLoadBy = 'sku';
        } else if ($this->getConfig('product_identifier') == 'attribute') {
            $this->attributeToLoadBy = $this->getConfig('product_identifier_attribute_code');
        } else if ($this->getConfig('product_identifier') == 'entity_id') {
            $this->attributeToLoadBy = 'entity_id';
        } else {
            throw new LocalizedException(__('Stock import: Attribute to use for identifying products not defined.'));
        }

        if ($this->attributeToLoadBy == 'sku') {
            $select = $this->readAdapter->select()
                ->from($this->getTableName('catalog_product_entity'), ['entity_id', 'type_id', 'sku'])
                ->where("LOWER(sku) in (" . $this->readAdapter->quote($this->productIdentifiers) . ")");
            $products = $this->readAdapter->fetchAll($select);

            foreach ($products as $product) {
                $this->productMap[trim(strtolower($product['sku']))] = $product['entity_id'];
                $this->productTypeMap[$product['entity_id']] = $product['type_id'];
                $this->productIdToSku[$product['entity_id']] = $product['sku'];
            }

            $productsNotFound = [];
            foreach ($this->productIdentifiers as $productIdentifier) {
                if (!isset($this->productMap[$productIdentifier])) {
                    array_push($productsNotFound, $productIdentifier);
                }
            }
            if (!empty($productsNotFound)) {
                $this->getLogEntry()->addDebugMessage(__('The following SKUs defined in the import file could not be found in the catalog: %1', join(", ", $productsNotFound)));
                #mail($this->moduleHelper->getDebugEmail(), 'Magento Stock Import Module @ ' . @$_SERVER['SERVER_NAME'], 'Stock Import products not found: ' . join(", ", $productsNotFound));
            }

            unset($products, $select);
        } else if ($this->getConfig('product_identifier') == 'attribute') {
            // Check if attribute exists
            $entityType = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY);
            $eavAttribute = $this->entityAttributeCollectionFactory->create()
                ->addFieldToFilter('entity_type_id', $entityType->getId())
                ->addFieldToFilter('attribute_code', $this->attributeToLoadBy)
                ->getFirstItem();
            if (!$eavAttribute || !$eavAttribute->getId()) {
                throw new LocalizedException(__('The supplied product attribute code used to identify products does not exist.'));
            }

            // Load product collection
            $productCollection = $this->productFactory->create()->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect($this->attributeToLoadBy)
                ->addAttributeToFilter($this->attributeToLoadBy, ['in' => str_replace("'", "", $this->productIdentifiers)]);

            foreach ($productCollection as $product) {
                $attrValue = $product->getData($this->attributeToLoadBy);
                $attrValue = trim(strtolower($attrValue));
                $this->productMap[$attrValue] = $product->getId();
                $this->productTypeMap[$product->getId()] = $product->getTypeId();
                $this->productIdToSku[$product->getId()] = $product->getSku();
            }
            unset($productCollection);
        } else if ($this->getConfig('product_identifier') == 'entity_id') {
            // We're supposed to use the entity_id to identify products.. that's great. Just use the IDs to load from tables etc. then!
            $select = $this->readAdapter->select()
                ->from($this->getTableName('catalog_product_entity'), ['entity_id', 'type_id', 'sku'])
                ->where("entity_id in (" . $this->readAdapter->quote($this->productIdentifiers) . ")");
            $products = $this->readAdapter->fetchAll($select);

            foreach ($products as $product) {
                if ($product['type_id'] == 'configurable' || $product['type_id'] == 'downloadable') {
                    continue;
                }
                $this->productMap[$product['entity_id']] = $product['entity_id'];
                $this->productTypeMap[$product['entity_id']] = $product['type_id'];
                $this->productIdToSku[$product['entity_id']] = $product['sku'];
            }

            $productsNotFound = [];
            foreach ($this->productIdentifiers as $productIdentifier) {
                if (!isset($this->productMap[$productIdentifier])) {
                    array_push($productsNotFound, $productIdentifier);
                }
            }
            if (!empty($productsNotFound)) {
                $this->getLogEntry()->addDebugMessage(__('The following product IDs defined in the import file could not be found in the catalog: %1', join(", ", $productsNotFound)));
                #mail($this->moduleHelper->getDebugEmail(), 'Stock Import Module @ ' . @$_SERVER['SERVER_NAME'], 'Stock Import - Products not found: ' . join(", ", $productsNotFound));
            }

            unset($products, $select);
        }
    }

    protected function applyFiltersToFoundProducts()
    {
        $profileConfig = $this->getProfile()->getConfiguration();
        for ($i = 1; $i <= self::$maxImportFilterCount; $i++) {
            if (!isset($profileConfig['import_filter_' . $i])) {
                continue;
            }
            $filter = $profileConfig['import_filter_' . $i];
            if (!array_key_exists('filter', $filter) ||
                !array_key_exists('attribute', $filter) ||
                !array_key_exists('condition', $filter) ||
                !array_key_exists('value', $filter)
            ) {
                $this->getLogEntry()->addDebugMessage(__('Warning: Filter %1 has not been configured properly. Filter skipped.', $i));
                continue;
            }
            if ($filter['filter'] == '' &&
                $filter['attribute'] == '' &&
                $filter['condition'] == '' &&
                $filter['value'] == ''
            ) {
                // Filter has not been set up - skip it.
                continue;
            }
            if ($filter['filter'] == '' ||
                $filter['attribute'] == '' ||
                $filter['condition'] == '' ||
                $filter['value'] == ''
            ) {
                $this->getLogEntry()->addDebugMessage(__('Warning: Filter %1 has not been configured properly. One more multiple filter fields are empty. Filter skipped.', $i));
                continue;
            }
            // Load products affected by filter, and "combine" products found + filter products so filter is applied.
            $productCollection = $this->productFactory->create()->getCollection()
                ->addAttributeToSelect('entity_id');
            // Determine product attribute to filter by, handle dropdown attributes
            $eavAttribute = $this->eavConfig->getAttribute('catalog_product', $filter['attribute']);
            if (!$eavAttribute || !$eavAttribute->getId()) {
                $this->getLogEntry()->addDebugMessage(__('Warning: Filter %1 uses a product attribute to filter which does not exist anymore. Filter skipped.', $i));
                continue;
            }
            if ($eavAttribute->getFrontendInput() == 'select') {
                $dropdownId = null;
                $attributeOptions = $eavAttribute->getSource()->getAllOptions();
                foreach ($attributeOptions as $option) {
                    if (strcasecmp($option['label'], $filter['value']) == 0 || $option['value'] == $filter['value']) {
                        $dropdownId = $option['value'];
                    }
                }
                if ($dropdownId === null) {
                    $this->getLogEntry()->addDebugMessage(__('Warning: Filter %1 tries to filter by a dropdown attribute value which does not exist. Please check the attribute "%2" and make sure the exact dropdown option ("%3") exists as an attribute option (Store View = Admin).', $i, $filter['attribute'], $filter['value']));
                    continue;
                } else {
                    $filter['value'] = $dropdownId;
                }
            } else {
                if ($filter['condition'] == 'like' || $filter['condition'] == 'nlike') {
                    $filter['value'] = '%' . $filter['value'] . '%';
                }
            }
            if ($filter['condition'] == 'neq') {
                $productCollection->addAttributeToFilter(
                    $filter['attribute'],
                    [
                        [$filter['condition'] => $filter['value']],
                        ['null' => true]
                    ],
                    'left'
                ); // Left join is required, so attribute values when joining attributes which don't have values which then are NULL can be checked
            } else {
                $productCollection->addAttributeToFilter(
                    $filter['attribute'],
                    [$filter['condition'] => $filter['value']]
                );
            }
            #echo (string)$productCollection->getSelect(); die();
            $foundProductIds = $productCollection->getAllIds();
            $removedProducts = 0;
            if ($filter['filter'] == 'include_only') {
                foreach ($this->productMap as $productIdentifier => $productId) {
                    if (!in_array($productId, $foundProductIds)) {
                        unset($this->productMap[$productIdentifier]);
                        $removedProducts++;
                    }
                }
            }
            if ($filter['filter'] == 'exclude') {
                foreach ($this->productMap as $productIdentifier => $productId) {
                    if (in_array($productId, $foundProductIds)) {
                        unset($this->productMap[$productIdentifier]);
                        $removedProducts++;
                    }
                }
            }
            $this->getLogEntry()->addDebugMessage(__('Filter %1 has removed/filtered %2 product(s) from the import files.', $i, $removedProducts));
        }
        #die();
    }

    /*
     * Get information about current stock settings, only for products we want to update though
     */
    protected function getCurrentStockInfo()
    {
        // Get stock_item information
        $select = $this->readAdapter->select()
            ->from($this->getTableName('cataloginventory_stock_item'),
                [
                    'product_id',
                    'qty',
                    'is_in_stock',
                    'stock_id',
                    'manage_stock',
                    'notify_stock_qty',
                    'use_config_manage_stock',
                    'min_qty',
                    'use_config_min_qty',
                    'min_sale_qty',
                    'use_config_min_sale_qty',
                    'max_sale_qty',
                    'use_config_max_sale_qty',
                    'backorders',
                    'use_config_backorders'
                ]
            )
            ->where("product_id in (" . join(",", array_values($this->productMap)) . ")");
        $stockItems = $this->readAdapter->fetchAll($select);

        foreach ($stockItems as $stockItem) {
            // Prepare qty field
            $stockItem['qty'] = sprintf('%.4f', $stockItem['qty']);
            $this->stockItems[$stockItem['stock_id']][$stockItem['product_id']] = $stockItem;
        }

        // Get stock_status information
        $select = $this->readAdapter->select()
            ->from(
                $this->getTableName('cataloginventory_stock_status'),
                ['product_id', 'qty', 'stock_status', 'stock_id']
            )
            ->where("product_id in (" . join(",", array_values($this->productMap)) . ")");
        $stockStatusItems = $this->readAdapter->fetchAll($select);

        foreach ($stockStatusItems as $stockStatusItem) {
            // Prepare qty field
            $stockStatusItem['qty'] = sprintf('%.4f', $stockStatusItem['qty']);
            $this->stockStatusItems[$stockStatusItem['stock_id']][$stockStatusItem['product_id']] = $stockStatusItem;
        }
    }

    /*
     * Get information about current stock items in MSI tables
     */
    protected function getMsiItemInfo()
    {
        // Get stock_item information
        $select = $this->readAdapter->select()
            ->from($this->getTableName('inventory_source_item'),
                [
                    'source_item_id',
                    'source_code',
                    'sku',
                    'quantity',
                    'status'
                ]
            )
            ->where("sku in (?)", array_values($this->productIdToSku));
        $stockItems = $this->readAdapter->fetchAll($select);

        foreach ($stockItems as $stockItem) {
            // Prepare qty field
            $stockItem['quantity'] = sprintf('%.4f', $stockItem['quantity']);
            $this->msiItems[$stockItem['source_code']][$stockItem['sku']] = $stockItem;
        }
    }

    /*
     * Get information about the current price levels for the products in the import file
     */
    protected function getCurrentPriceInfo()
    {
        if (self::$importPrices) {
            $priceAttributeId = $this->entityAttributeFactory->create()->getIdByCode('catalog_product', 'price');
            if ($priceAttributeId) {
                $select = $this->readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_decimal'))
                    ->where('attribute_id = ?', $priceAttributeId)
                    ->where($this->productEntityDecimalFieldName . " in (" . join(",", array_values($this->productMap)) . ")");
                if ($this->priceScope != 0) { // Per website
                    $configUpdateStoreId = $this->getConfig('price_update_store_id');
                    if (is_array($configUpdateStoreId)) {
                        $configUpdateStoreId = array_filter($configUpdateStoreId);
                    }
                    $storeIds = false;
                    if (!empty($configUpdateStoreId)) {
                        $storeIds = join(",", $configUpdateStoreId);
                        if (!empty($storeIds)) {
                            $select->where("store_id in (" . $storeIds . ")");
                        }
                    }
                    if ($storeIds === false) {
                        $select->where("store_id = ?", 0);
                    }
                } else {
                    $select->where("store_id = ?", 0);
                }
                $currentPrices = $this->readAdapter->fetchAll($select);

                foreach ($currentPrices as $currentPrice) {
                    $this->prices[$currentPrice['store_id']][$currentPrice[$this->productEntityDecimalFieldName]] = ['price' => sprintf('%.4f', $currentPrice['value'])];
                }
            } else {
                throw new LocalizedException(__('Error while trying to get current price info. The price attribute could not be found.'));
            }
        }
        if (self::$importSpecialPrices) {
            $priceAttributeId = $this->entityAttributeFactory->create()->getIdByCode('catalog_product', 'special_price');
            if ($priceAttributeId) {
                $select = $this->readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_decimal'))
                    ->where('attribute_id = ?', $priceAttributeId)
                    ->where($this->productEntityDecimalFieldName . " in (" . join(",", array_values($this->productMap)) . ")");
                if ($this->priceScope != 0) { // Per website
                    $configUpdateStoreId = $this->getConfig('price_update_store_id');
                    if (is_array($configUpdateStoreId)) {
                        $configUpdateStoreId = array_filter($configUpdateStoreId);
                    }
                    $storeIds = false;
                    if (!empty($configUpdateStoreId)) {
                        $storeIds = join(",", $configUpdateStoreId);
                        if (!empty($storeIds)) {
                            $select->where("store_id in (" . $storeIds . ")");
                        }
                    }
                    if ($storeIds === false) {
                        $select->where("store_id = ?", 0);
                    }
                } else {
                    $select->where("store_id = ?", 0);
                }
                $currentPrices = $this->readAdapter->fetchAll($select);

                foreach ($currentPrices as $currentPrice) {
                    if (!empty($currentPrice['value'])) {
                        $this->specialPrices[$currentPrice['store_id']][$currentPrice[$this->productEntityDecimalFieldName]] = ['special_price' => sprintf('%.4f', $currentPrice['value'])];
                    } else {
                        $this->specialPrices[$currentPrice['store_id']][$currentPrice[$this->productEntityDecimalFieldName]] = ['special_price' => ''];
                    }
                }
            } else {
                throw new LocalizedException(__('Error while trying to get current special price info. The special price attribute could not be found.'));
            }
        }
        if (self::$importCost) {
            $costAttributeId = $this->entityAttributeFactory->create()->getIdByCode('catalog_product', 'cost');
            if ($costAttributeId) {
                $select = $this->readAdapter->select()
                    ->from($this->getTableName('catalog_product_entity_decimal'))
                    ->where('attribute_id = ?', $costAttributeId)
                    ->where($this->productEntityDecimalFieldName . " in (" . join(",", array_values($this->productMap)) . ")");
                if ($this->priceScope != 0) { // Per website
                    $configUpdateStoreId = $this->getConfig('price_update_store_id');
                    if (is_array($configUpdateStoreId)) {
                        $configUpdateStoreId = array_filter($configUpdateStoreId);
                    }
                    $storeIds = false;
                    if (!empty($configUpdateStoreId)) {
                        $storeIds = join(",", $configUpdateStoreId);
                        if (!empty($storeIds)) {
                            $select->where("store_id in (" . $storeIds . ")");
                        }
                    }
                    if ($storeIds === false) {
                        $select->where("store_id = ?", 0);
                    }
                } else {
                    $select->where("store_id = ?", 0);
                }
                $currentPrices = $this->readAdapter->fetchAll($select);

                foreach ($currentPrices as $currentPrice) {
                    if (!empty($currentPrice['value'])) {
                        $this->costValues[$currentPrice['store_id']][$currentPrice[$this->productEntityDecimalFieldName]] = ['cost' => sprintf('%.4f', $currentPrice['value'])];
                    } else {
                        $this->costValues[$currentPrice['store_id']][$currentPrice[$this->productEntityDecimalFieldName]] = ['cost' => ''];
                    }
                }
            } else {
                self::$importCost = false;
                #throw new LocalizedException(__('Error while trying to get current "cost" info. The cost attribute could not be found.'));
            }
        }
        #var_dump($this->prices);
        #die();
    }

    /*
     * Get information about the current price levels for the products in the import file
     */
    protected function getCurrentProductInfo()
    {
        $attributeId = $this->entityAttributeFactory->create()->getIdByCode('catalog_product', 'status');
        if ($attributeId) {
            $select = $this->readAdapter->select()
                ->from($this->getTableName('catalog_product_entity_int'))
                ->where('attribute_id = ?', $attributeId)
                ->where($this->productEntityDecimalFieldName . " in (" . join(",", array_values($this->productMap)) . ")");
            $configUpdateStoreId = $this->getConfig('price_update_store_id');
            if (is_array($configUpdateStoreId)) {
                $configUpdateStoreId = array_filter($configUpdateStoreId);
            }
            $storeIds = false;
            if (!empty($configUpdateStoreId)) {
                $storeIds = join(",", $configUpdateStoreId);
                if (!empty($storeIds)) {
                    $select->where("store_id in (" . $storeIds . ")");
                }
            }
            if ($storeIds === false) {
                $select->where("store_id = ?", 0);
            }
            $products = $this->readAdapter->fetchAll($select);

            foreach ($products as $product) {
                $this->productStatus[$product['store_id']][$product[$this->productEntityDecimalFieldName]] = ['status' => $product['value']];
            }
        }
    }

    protected function getCurrentProductAttributeValues($fields)
    {
        foreach ($fields as $field) {
            $isCustomAttribute = false;
            $attributeCode = preg_replace('/^cpa_/', '', $field, -1, $isCustomAttribute);
            if ($isCustomAttribute) {
                $productAttribute = $this->entityAttributeFactory->create()->loadByCode('catalog_product', $attributeCode);
                if ($productAttribute && $productAttribute->getId()) {
                    $productAttribute->setStoreId(0);
                    $attributeUpdateStoreIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
                    if ((int)$productAttribute->getIsGlobal() !== \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL) {
                        $configUpdateStoreId = $this->getConfig('price_update_store_id');
                        if (is_array($configUpdateStoreId)) {
                            $configUpdateStoreId = array_filter($configUpdateStoreId);
                        }
                        if ($configUpdateStoreId !== '' && !empty($configUpdateStoreId)) {
                            $attributeUpdateStoreIds = $configUpdateStoreId;
                        }
                    }
                    // Set store (for attribute values / source/dropdown attributes)
                    // $productAttribute->setStoreId($updateStoreId);
                    // Push into array
                    $this->customProductAttributes[$attributeCode] = $productAttribute;
                    foreach ($attributeUpdateStoreIds as $updateStoreId) {
                        $this->customProductAttributeValues[$updateStoreId][$attributeCode] = [];
                    }
                    $select = $this->readAdapter->select()
                        ->from($productAttribute->getBackendTable())
                        ->where('attribute_id = ?', $productAttribute->getId())
                        ->where($this->productEntityDecimalFieldName . " in (" . join(",", array_values($this->productMap)) . ")")
                        ->where("store_id in (" . join(",", $attributeUpdateStoreIds) . ")");
                    $attributeValues = $this->readAdapter->fetchAll($select);

                    foreach ($attributeValues as $attributeValue) {
                        $this->customProductAttributeValues[$attributeValue['store_id']][$attributeCode][$attributeValue[$this->productEntityDecimalFieldName]] = $attributeValue['value'];
                    }

                    // Add empty values for attributes which don't have entries in the DB
                    foreach ($attributeUpdateStoreIds as $updateStoreId) {
                        foreach ($this->productMap as $productIdentifier => $productId) {
                            if (!array_key_exists($productId, $this->customProductAttributeValues[$updateStoreId][$attributeCode])) {
                                $this->customProductAttributeValues[$updateStoreId][$attributeCode][$productId] = '';
                            }
                        }
                    }
                } else {
                    throw new LocalizedException(__('Error while trying to load attribute %1, attribute not found. Remove it from the import profile.', $attributeCode));
                }
            }
        }
    }

    /*
     * Update stock level for product
     */
    public function processItem($productIdentifier, $updateData)
    {
        // Result (and debug information) returned to observer
        $importResult = ['error' => 'Nothing happened yet.'];

        if (isset($updateData['product_identifier'])) {
            unset($updateData['product_identifier']);
        }
        $productIdentifier = strtolower($productIdentifier);

        if (!isset($updateData['stock_id']) || empty($updateData['stock_id'])) {
            $updateData['stock_id'] = 1;
        } else {
            $updateData['stock_id'] = intval($updateData['stock_id']);
        }

        if ($this->entityHelper->getMagentoMSISupport() && (!isset($updateData['source_code']) || empty($updateData['source_code']))) {
            $updateData['source_code'] = 'default';
        }

        $msiNote = '';
        if (isset($updateData['source_code'])) {
            $msiNote = __(' [MSI Source: %1]', $updateData['source_code']);
        }

        // Update stock_item, stock_status and eventually the price
        if (isset($this->productMap[$productIdentifier])) {
            $productId = $this->productMap[$productIdentifier];
            // Current import result.. nothing has changed yet
            $importResult = ['changed' => false, 'debug' => __("Product '%1' was found in Magento, but no fields have changed. Identified product ID is %2.", $productIdentifier, $productId)];

            // Adjust stock level by pending/processing orders
            if ($this->getConfigFlag('adjust_stock_pending_orders')) {
                $orderStatuses = $this->getConfig('adjust_stock_pending_orders_statuses');
                if (empty($orderStatuses) || !is_array($orderStatuses)) {
                    $orderStatuses = ['pending', 'processing'];
                }
                $operationMode = 'decrease';
                if ($this->getConfig('adjust_stock_pending_orders_mode') == 2) {
                    $operationMode = 'increase';
                }
                if (isset($updateData['qty'])) {
                    $orderItemCollection = $this->objectManager->create('\Magento\Sales\Model\Order\ItemFactory')->create()->getCollection();
                    $orderItemCollection
                        ->getSelect()
                        ->joinInner(
                            ['order' => $this->getTableName('sales_order')],
                            'order.entity_id = main_table.order_id'
                        )
                        ->where('main_table.product_id=?', $productId)
                        ->where('order.status in (?)', $orderStatuses);
                    if ($orderItemCollection->count() > 0) {
                        $blockedQty = 0;
                        foreach ($orderItemCollection as $orderItem) {
                            $blockedQty += (int)$orderItem->getQtyOrdered() - (int)$orderItem->getQtyShipped();
                        }
                        if ($operationMode == 'decrease') {
                            $updateData['qty'] = $updateData['qty'] - $blockedQty;
                        } else {
                            $updateData['qty'] = $updateData['qty'] + $blockedQty;
                        }
                    }
                    #var_dump($orderItemCollection->count(), $productId, $updateData['qty'], $blockedQty); die();
                }
            }
            // End stock level adjustment by pending/processing orders

            // Fetch updated fields
            $updatedFields = $this->getUpdatedFields($updateData, $productId); // See if anything has changed..

            // Support for notifications sent by Mageants OutofStockNotification extension
            if (isset($updatedFields['is_in_stock']) && $updatedFields['is_in_stock']) {
                if ($this->utilsHelper->isExtensionInstalled('Mageants_OutofStockNotification')) {
                    $stockHelper = $this->objectManager->get('\Mageants\OutofStockNotification\Helper\Data');
                    if ($stockHelper->isEnable()) {
                        $stockHelper->sendNotifications($stockHelper->getStockNotifyCustomer(\Magento\Store\Model\Store::DEFAULT_STORE_ID), $this->productIdToSku[$productId]);
                    }
                }
            }

            if (self::$importStock) {
                // Check is supported product type
                $productType = $this->productTypeMap[$productId];
                if ($productType !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
                    if (!isset($updateData['source_code']) || empty($updateData['source_code']) || $updateData['source_code'] == 'default') {
                        // Only update for source_code=default (MSI) or when no source_code is set
                        // stock_item routine:
                        if (isset($this->stockItems[$updateData['stock_id']][$productId])) {
                            // This is a known stock_item entry
                            if (!empty($updatedFields)) {
                                // Something has changed, update stock item
                                $importResult = $this->updateStockItem($productId, $productIdentifier, $updatedFields, $updateData);
                                // Push product ID to modified stock items.
                                array_push($this->modifiedStockItems, $productId);
                            } else {
                                // Nothing has changed
                                if ($this->getTestMode()) {
                                    #return $importResult;
                                }
                            }
                        } else {
                            // New stock item, insert stock item
                            $importResult = $this->insertStockItem($productId, $productIdentifier, $updateData);
                            // Push product ID to modified stock items.
                            array_push($this->modifiedStockItems, $productId);
                        }
                    }

                    if (!$this->getTestMode()) {
                        if (!isset($updateData['source_code']) || empty($updateData['source_code']) || $updateData['source_code'] == 'default') {
                            // Only update for source_code=default (MSI) or when no source_code is set
                            // stock_status routine, update only when not in test_mode
                            if (isset($this->stockStatusItems[$updateData['stock_id']][$productId])) {
                                // This is a known stock_status entry
                                if (!empty($updatedFields)) {
                                    // Something has changed, update stock_status
                                    $this->updateStockStatus($productId, $updatedFields, $updateData);
                                    // Push product ID to modified stock items.
                                    if (!isset($this->modifiedStockItems[$productId])) {
                                        array_push($this->modifiedStockItems, $productId);
                                    }
                                }
                            } else {
                                // New stock item, insert stock item
                                $this->insertStockStatus($productId, $updateData);
                                // Push product ID to modified stock items.
                                if (!isset($this->modifiedStockItems[$productId])) {
                                    array_push($this->modifiedStockItems, $productId);
                                }
                            }
                        }

                        if ($this->entityHelper->getMagentoMSISupport()) {
                            // MSI stock item routine, update MSI tables
                            $productSku = $this->productIdToSku[$productId];
                            $updatedMsiFields = $this->getUpdatedMsiFields($updateData, $productSku);
                            if (isset($this->msiItems[$updateData['source_code']][$productSku])) {
                                // This is a known MSI entry
                                if (!empty($updatedMsiFields)) {
                                    // Something has changed, update stock_status
                                    $this->updateMsiItem($productSku, $updatedMsiFields, $updateData);
                                    if ($importResult['changed'] === false) {
                                        $tempUpdatedFields = $updatedMsiFields;
                                        array_walk($tempUpdatedFields, function(&$i, $k) {
                                            $i = " \"$k\"=\"$i\"";
                                        });
                                        $importResult = ['changed' => true, 'debug' => __("Product '%1' (MSI Item) stock updated in Magento: %2.", $productSku, implode("", $tempUpdatedFields))];
                                    }
                                    // Push product ID to modified stock items.
                                    if (!isset($this->modifiedMsiItems[$productId])) {
                                        array_push($this->modifiedMsiItems, $productId);
                                    }
                                    if (!isset($this->modifiedStockItems[$productId])) {
                                        array_push($this->modifiedStockItems, $productId);
                                    }
                                }
                            } else {
                                // New MSI item, insert stock item
                                $this->insertMsiitem($productSku, $updateData);
                                // Push product ID to modified stock items.
                                if (!isset($this->modifiedMsiItems[$productId])) {
                                    array_push($this->modifiedMsiItems, $productId);
                                }
                                if (!isset($this->modifiedStockItems[$productId])) {
                                    array_push($this->modifiedStockItems, $productId);
                                }
                                if ($importResult['changed'] === false) {
                                    $tempUpdatedFields = $updatedMsiFields;
                                    array_walk($tempUpdatedFields, function(&$i, $k) {
                                        $i = " \"$k\"=\"$i\"";
                                    });
                                    $importResult = ['changed' => true, 'debug' => __("Product '%1' (MSI Item) stock created in Magento: %2.", $productSku, implode("", $tempUpdatedFields))];
                                }
                            }
                        }
                    }
                }
            }

            $productFieldsUpdated = [];
            
            // Which stores should be updated
            $priceUpdateStoreIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
            $configUpdateStoreId = $this->getConfig('price_update_store_id');
            if (is_array($configUpdateStoreId)) {
                foreach ($configUpdateStoreId as &$storeId) {
                    if ($storeId === '') {
                        $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                    }
                }
            }
            if ($configUpdateStoreId !== '' && !empty($configUpdateStoreId)) {
                $priceUpdateStoreIds = $configUpdateStoreId;
            }
            $attributeUpdateStoreIds = $priceUpdateStoreIds;
            if ($this->priceScope === 0) { // Global
                $priceUpdateStoreIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
            }

            // Tier price routine
            if (version_compare($this->utilsHelper->getMagentoVersion(), '2.2', '>=')) {
                $tierPriceUpdated = false;
                // Very first loop - get all tier prices to add/update
                $tierPrices = [];
                foreach ($updateData as $field => $newValue) {
                    $customerGroupId = preg_replace('/^tier_price\:/', '', $field, -1, $isTierPriceField);
                    if (!$isTierPriceField) {
                        continue;
                    }
                    if ($customerGroupId === 'all') {
                        $customerGroupId = Group::CUST_GROUP_ALL;
                    }
                    $tierPrices[$customerGroupId] = $newValue;
                }
                // Loop through fields and see if any tier prices are included
                if (!empty($tierPrices)) {
                    $tierPriceManagement = $this->objectManager->get('\Magento\Catalog\Api\ScopedProductTierPriceManagementInterface');
                    $tierPriceFactory = $this->objectManager->get('\Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory');
                    $tierPriceExtensionFactory = $this->objectManager->get('\Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory');
                    $priceModifier = $this->objectManager->get('\Magento\Catalog\Model\Product\PriceModifier');
                    $productSku = $this->productIdToSku[$productId];
                    // Remove tier prices
                    //$product = $this->productRepository->getById($productId);
                    //$product->setData('tier_price', []);
                    //$this->productRepository->save($product); // Required to save tier price unfortunately
                    $product = $this->productRepository->get($productSku, ['edit_mode' => true], 0); // Store ID 0 required to get "all" tier prices
                    $hasChanged = false;
                    $tierPricesFromProduct = $product->getData('tier_price');
                    if (is_array($tierPricesFromProduct)) {
                        foreach ($tierPricesFromProduct as $price) {
                            if (isset($tierPrices[$price['cust_group']])) {
                                $priceModifier->removeTierPrice(
                                    $product,
                                    $price['cust_group'],
                                    $price['price_qty'],
                                    $price['website_id']
                                );
                                $hasChanged = true;
                            }
                        }
                    }
                    if ($hasChanged) {
                        $this->productRepository->save($product); // Required to save tier price unfortunately
                    }
                }
                foreach ($updateData as $field => $newValue) {
                    $customerGroupId = preg_replace('/^tier_price\:/', '', $field, -1, $isTierPriceField);
                    if ($customerGroupId === 'all') {
                        $customerGroupId = Group::CUST_GROUP_ALL;
                    }
                    if ($isTierPriceField) {
                        if (stristr($newValue, '__EMPTY__') === false) {
                            $websitesToUpdate = [];
                            foreach ($priceUpdateStoreIds as $updateStoreId) {
                                $websiteId = $this->storeManager->getStore($updateStoreId)->getWebsiteId();
                                $websitesToUpdate[$websiteId] = ['website_id' => $websiteId, 'store_id' => $updateStoreId];
                            }
                            foreach ($websitesToUpdate as $website) {
                                $parsedTierPrices = explode(';', $newValue);
                                foreach ($parsedTierPrices as $parsedTierPrice) {
                                    $tierPriceDetail = explode(':', $parsedTierPrice);
                                    if (!isset($tierPriceDetail[1])) {
                                        continue;
                                    }
                                    $tierQty = floatval($tierPriceDetail[0]);
                                    $tierValue = floatval($tierPriceDetail[1]);
                                    /** @var \Magento\Catalog\Api\Data\ProductTierPriceInterface $tierPrice */
                                    $tierPrice = $tierPriceFactory->create()->setExtensionAttributes($tierPriceExtensionFactory->create());
                                    $tierPrice->setCustomerGroupId($customerGroupId);
                                    $tierPrice->setQty($tierQty);
                                    $tierPrice->setValue($tierValue);
                                    if (strstr($tierPriceDetail[1], '%') !== false) {
                                        $tierPrice->getExtensionAttributes()->setPercentageValue($tierValue);
                                    }
                                    $tierPrice->getExtensionAttributes()->setWebsiteId($website['website_id']);
                                    $tierPriceManagement->add($productSku, $tierPrice);
                                }
                                $productFieldsUpdated[$website['store_id']][$field] = $newValue;
                            }
                        }
                        $tierPriceUpdated = true;
                    }
                }
                if ($tierPriceUpdated) {
                    array_push($this->updatedTierPrices, $productId);
                }
            }

            // price update routine
            if (self::$importPrices) {
                if (!empty($updateData) && isset($updateData['price'])) {
                    if ($updateData['price'] == "__EMPTY__") {
                        $newPrice = null;
                    } else {
                        $newPrice = sprintf('%.4f', $updateData['price']);
                    }
                    foreach ($priceUpdateStoreIds as $updateStoreId) {
                        if (isset($this->prices[$updateStoreId]) && isset($this->prices[$updateStoreId][$productId])) {
                            $currentPrice = $this->prices[$updateStoreId][$productId]['price'];
                            if ($currentPrice !== $newPrice || $newPrice == '') {
                                if (!$this->getTestMode()) {
                                    $this->productAction->updateAttributes([$productId], ['price' => $newPrice], $updateStoreId);
                                }
                                // Price has changed.
                                array_push($this->updatedPrices, $productId);
                                $productFieldsUpdated[$updateStoreId]['price'] = $currentPrice . ' => ' . $newPrice;
                            }
                        } else {
                            if (!$this->getTestMode()) {
                                $this->productAction->updateAttributes([$productId], ['price' => $newPrice], $updateStoreId);
                            }
                            // Price has changed.
                            array_push($this->updatedPrices, $productId);
                            $productFieldsUpdated[$updateStoreId]['price'] = 'null => ' . $newPrice;
                        }
                    }
                }
            }

            if (self::$importSpecialPrices && !$this->getTestMode()) {
                if (!empty($updateData) && isset($updateData['special_price'])) {
                    foreach ($priceUpdateStoreIds as $updateStoreId) {
                        if (isset($this->specialPrices[$updateStoreId]) && isset($this->specialPrices[$updateStoreId][$productId])) {
                            $currentPrice = $this->specialPrices[$updateStoreId][$productId]['special_price'];
                        } else {
                            $currentPrice = null;
                        }
                        if ($updateData['special_price'] != '') {
                            $newPrice = sprintf('%.4f', $updateData['special_price']);
                        } else {
                            $newPrice = '';
                        }
                        $fromDate = date('Y-m-d');
                        if ($newPrice === "0.0000") {
                            $newPrice = "";
                            $fromDate = "";
                        }
                        if ($currentPrice !== $newPrice || $newPrice == '') {
                            $this->productAction->updateAttributes([$productId], ['special_price' => $newPrice], $updateStoreId);
                            $this->productAction->updateAttributes([$productId], ['special_from_date' => $fromDate], $updateStoreId);
                            // Special price has changed.
                            array_push($this->updatedSpecialPrices, $productId);
                            $productFieldsUpdated[$updateStoreId]['special_price'] = $currentPrice . ' => ' . $newPrice;
                            $productFieldsUpdated[$updateStoreId]['special_from_date'] = $fromDate;
                        }
                    }
                } else {
                    foreach ($priceUpdateStoreIds as $updateStoreId) {
                        if (isset($this->specialPrices[$updateStoreId]) && isset($this->specialPrices[$updateStoreId][$productId])) {
                            if (!empty($this->specialPrices[$updateStoreId][$productId]['special_price'])) {
                                $this->productAction->updateAttributes([$productId], ['special_price' => ''], $updateStoreId);
                                $this->productAction->updateAttributes([$productId], ['special_from_date' => ''], $updateStoreId);
                                array_push($this->updatedSpecialPrices, $productId);
                                $productFieldsUpdated[$updateStoreId]['special_price'] = '';
                                $productFieldsUpdated[$updateStoreId]['special_from_date'] = '';
                            }
                        }
                    }
                }
            }

            if (self::$importCost && !$this->getTestMode()) {
                if (!empty($updateData) && isset($updateData['cost'])) {
                    foreach ($priceUpdateStoreIds as $updateStoreId) {
                        if (isset($this->costValues[$updateStoreId]) && isset($this->costValues[$updateStoreId][$productId])) {
                            $currentPrice = $this->costValues[$updateStoreId][$productId]['cost'];
                        } else {
                            $currentPrice = null;
                        }
                        if ($updateData['cost'] != '') {
                            $newPrice = sprintf('%.4f', $updateData['cost']);
                        } else {
                            $newPrice = '';
                        }
                        if (stristr($updateData['cost'], '__EMPTY__') !== false) {
                            $newPrice = null;
                        }
                        if ($currentPrice !== $newPrice || $newPrice == '') {
                            $this->productAction->updateAttributes([$productId], ['cost' => $newPrice], $updateStoreId);
                            // Cost has changed.
                            array_push($this->updatedCostValues, $productId);
                            $productFieldsUpdated[$updateStoreId]['cost'] = $currentPrice . ' => ' . $newPrice;
                        }
                    }
                } else {
                    foreach ($priceUpdateStoreIds as $updateStoreId) {
                        if (isset($this->costValues[$updateStoreId]) && isset($this->costValues[$updateStoreId][$productId])) {
                            if (!empty($this->costValues[$updateStoreId][$productId]['cost'])) {
                                $this->productAction->updateAttributes([$productId], ['cost' => ''], $updateStoreId);
                                $productFieldsUpdated[$updateStoreId]['cost'] = '';
                            }
                        }
                    }
                }
            }

            if (self::$importProductStatus && !$this->getTestMode()) {
                if (!empty($updateData) && isset($updateData['status'])) {
                    foreach ($attributeUpdateStoreIds as $updateStoreId) {
                        if (isset($this->productStatus[$updateStoreId]) && isset($this->productStatus[$updateStoreId][$productId])) {
                            $currentStatus = $this->productStatus[$updateStoreId][$productId]['status'];
                        } else {
                            $currentStatus = null;
                        }
                        if ($updateData['status'] != '') {
                            $updateStatus = strtolower($updateData['status']);
                            $newStatus = null;
                            if ($updateStatus == 'yes' || $updateStatus == '1' || $updateStatus == 'enabled' || $updateStatus == 'ja' || $updateStatus == 'true') {
                                $newStatus = 1;
                            }
                            if ($updateStatus == 'no' || $updateStatus == '0' || $updateStatus == 'disabled' || $updateStatus == 'nein' || $updateStatus == 'false') {
                                $newStatus = 2;
                            }
                            if ($newStatus === null && $updateStatus !== '') {
                                throw new LocalizedException(__('An invalid value was imported for the product status column. It should contain values like "Enabled" or "Disabled".'));
                            }
                            if ($currentStatus != $newStatus) {
                                if ($newStatus === 1) {
                                    $this->productAction->updateAttributes([$productId], ['status' => ProductStatus::STATUS_ENABLED], $updateStoreId);
                                } else {
                                    $this->productAction->updateAttributes([$productId], ['status' => ProductStatus::STATUS_DISABLED], $updateStoreId);
                                }
                                $productFieldsUpdated[$updateStoreId]['status'] = $currentStatus . ' => ' . $newStatus;
                                array_push($this->updatedProductStatuses, $productId);
                            }
                        }
                    }
                }
            }

            if (self::$importCustomAttributes && !$this->getTestMode() && !empty($updateData)) {
                $attributesToUpdate = [];
                foreach ($updateData as $field => $newValue) {
                    if ($newValue === '') {
                        continue;
                    }
                    if (stristr($newValue, '__EMPTY__') !== false) {
                        $newValue = '';
                    }
                    $isCustomAttribute = false;
                    $attributeCode = preg_replace('/^cpa_/', '', $field, -1, $isCustomAttribute);
                    if ($isCustomAttribute) {
                        /** @var Attribute $productAttribute */
                        $productAttribute = $this->customProductAttributes[$attributeCode];
                        $tempStoreIds = $attributeUpdateStoreIds;
                        if ((int)$productAttribute->getIsGlobal() === \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL) {
                            $tempStoreIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID]; // Global attribute
                        }
                        foreach ($tempStoreIds as $updateStoreId) {
                            if (isset($this->customProductAttributeValues[$updateStoreId]) && isset($this->customProductAttributeValues[$updateStoreId][$attributeCode]) && isset($this->customProductAttributeValues[$updateStoreId][$attributeCode][$productId])) {
                                $currentValue = $this->customProductAttributeValues[$updateStoreId][$attributeCode][$productId];
                            } else {
                                $currentValue = null;
                            }
                            $originalValue = $newValue;
                            if ($productAttribute->getFrontendInput() == 'multiselect') {
                                $selectValues = $productAttribute->getSource()->toOptionArray();
                                $splitValues = explode(',', $originalValue);
                                if ($newValue !== '') {
                                    $newValue = [];
                                    foreach ($splitValues as $splitValue) {
                                        foreach ($selectValues as $selectValue) {
                                            if ($selectValue['value'] == $splitValue || $selectValue['label'] == $splitValue) {
                                                $newValue[] = $selectValue['value'];
                                                break 1;
                                            }
                                        }
                                    }
                                    $newValue = implode(',', $newValue);
                                }
                            } else if ($productAttribute->usesSource() && $productAttribute->getAttributeCode() !== 'country_of_manufacture') {
                                $currentValue = intval($currentValue);
                                $tempVal = $productAttribute->getSource()->getOptionId($newValue);
                                $newValue = intval(($tempVal !== null) ? $tempVal : $originalValue);
                            }
                            if ($productAttribute->getBackendType() == 'decimal') {
                                if ($newValue === '') {
                                    $newValue = null;
                                } else {
                                    $newValue = sprintf('%.4f', $newValue);
                                }
                            }
                            //var_dump($productAttribute->getSource()->toOptionArray(), $productAttribute->usesSource(), $attributeCode, $currentValue, $newValue);
                            if ($currentValue !== $newValue) {
                                $attributesToUpdate[$updateStoreId][$attributeCode] = $newValue;
                                array_push($this->updatedCustomProductAttributes, $productId);
                                if ($originalValue !== $newValue && $productAttribute->usesSource()) {
                                    $productFieldsUpdated[$updateStoreId][$attributeCode] = $currentValue . ' => ' . sprintf('%s [ID: %s]', $originalValue, print_r($newValue, true));
                                } else {
                                    $productFieldsUpdated[$updateStoreId][$attributeCode] = $currentValue . ' => ' . $newValue;
                                }
                            }
                        }
                    }
                }
                //var_dump($attributesToUpdate); die();
                if (!empty($attributesToUpdate)) {
                    foreach ($attributesToUpdate as $updateStoreId => $storeAttributes) {
                        $this->productAction->updateAttributes([$productId], $storeAttributes, $updateStoreId);
                    }
                }
            }

            if (is_array($productFieldsUpdated) && !empty($productFieldsUpdated)) {
                $tempProductFields = $productFieldsUpdated;
                array_walk($tempProductFields, function(&$attributes, $storeId) {
                    $origAttributes = $attributes;
                    $attributes = __('Store ID %1:', $storeId);
                    foreach ($origAttributes as $key => $value) {
                        $attributes .= " \"$key: $value\"";
                    }
                });
                // Add debug messages to $importResult
                if (isset($importResult['error']) || (isset($importResult['changed']) && $importResult['changed'] === false)) {
                    if ($this->getTestMode()) {
                        $importResult = ['changed' => true, 'debug' => __("Product '%1' would have been updated. Identified product ID is %2. Updated fields: %3", $productIdentifier, $productId, implode(" | ", $tempProductFields))];
                    } else {
                        $importResult = ['changed' => true, 'debug' => __("Product '%1' has been updated. Identified product ID is %2. Updated fields: %3", $productIdentifier, $productId, implode(" | ", $tempProductFields))];
                    }
                } else if (isset($importResult['changed']) && $importResult['changed'] === true) {
                    $importResult['debug'] .= implode("", $tempProductFields);
                }
            }
        } else {
            // Product not found.
            #$importResult = array('error' => __("Product '%1' could not be found in Magento. We tried to identify the product by using the attribute '%2'.", $productIdentifier, $this->attributeToLoadBy));
            $importResult = ['changed' => false];
        }

        if (isset($importResult['debug'])) {
            $importResult['debug'] = $importResult['debug'] . $msiNote;
        }

        return $importResult;
    }

    /*
     * See which fields changed and if necessary modify other fields based on fields - example qty <= 0 -> is_in_stock = false
     */
    protected function getUpdatedFields($updateData, $productId)
    {
        $updatedFields = [];
        $productType = $this->productTypeMap[$productId];

        /*if (empty($this->stockItems)) {
            return $updatedFields;
        }*/

        /*
         * First run: See which values changed, adjust values based on that and return fields to update.
         */
        if (!isset($this->stockItems[$updateData['stock_id']]) || !isset($this->stockItems[$updateData['stock_id']][$productId])) {
            // New stock item/status
            foreach ($updateData as $field => $newValue) {
                if ($field == 'price' || $field == 'special_price' || $field == 'cost' || $field == 'stock_id' || $field == 'status' || preg_match('/^cpa_/', $field)) { // Do not process these here.
                    continue;
                }
                if ($productType === Configurable::TYPE_CODE) {
                    // Only certain fields can be updated for configurable products. Filter others.
                    $allowedFields = ['is_in_stock', 'manage_stock', 'use_config_manage_stock', 'notify_stock_qty', 'backorders', 'min_qty', 'min_sale_qty', 'max_sale_qty'];
                    if (!in_array($field, $allowedFields)) {
                        continue;
                    }
                }
                // Type casting - everything coming from the database is a string (apparently, at least in my tests)
                if ($field == 'manage_stock' || $field == 'use_config_manage_stock' || $field == 'is_in_stock' || $field == 'backorders') {
                    $newValue = (int)$newValue;
                }
                if ($field == 'notify_stock_qty' || $field == 'min_qty' || $field == 'min_sale_qty' || $field == 'max_sale_qty') {
                    $newValue = (float)$newValue;
                }
                $updatedFields[$field] = $newValue;
            }
        } else {
            // Already existing
            $stockItem = $this->stockItems[$updateData['stock_id']][$productId];
            foreach ($updateData as $field => $newValue) {
                foreach ($stockItem as $stockField => $stockValue) {
                    if ($stockField == 'price' || $stockField == 'special_price' || $stockField == 'cost' || $stockField == 'stock_id' || $stockField == 'status' || preg_match('/^cpa_/', $field)) { // Do not process these here.
                        continue;
                    }
                    if ($productType === Configurable::TYPE_CODE) {
                        // Only certain fields can be updated for configurable products. Filter others.
                        $allowedFields = ['is_in_stock', 'manage_stock', 'use_config_manage_stock', 'notify_stock_qty', 'backorders', 'min_qty', 'min_sale_qty', 'max_sale_qty'];
                        if (!in_array($field, $allowedFields)) {
                            continue;
                        }
                    }
                    if ($field == $stockField) {
                        // Type casting - everything coming from the database is a string (apparently, at least in my tests)
                        if ($stockField == 'manage_stock' || $stockField == 'use_config_manage_stock' || $stockField == 'is_in_stock' || $stockField == 'backorders') {
                            $stockValue = (int)$stockValue; // Should be an integer coming from the database.
                            $newValue = (int)$newValue;
                        }
                        if ($stockField == 'notify_stock_qty' || $stockField == 'min_qty' || $stockField == 'min_sale_qty' || $stockField == 'max_sale_qty') {
                            $stockValue = (float)$stockValue;
                            $newValue = (float)$newValue;
                        }
                        // Field types
                        /*
                         $result[StockItemInterface::MANAGE_STOCK] = (int)$stockItem->getManageStock();
                         $result[StockItemInterface::QTY] = (float)$stockItem->getQty();
                         $result[StockItemInterface::MIN_QTY] = (float)$stockItem->getMinQty();
                         $result[StockItemInterface::MIN_SALE_QTY] = (float)$stockItem->getMinSaleQty();
                         $result[StockItemInterface::MAX_SALE_QTY] = (float)$stockItem->getMaxSaleQty();
                         $result[StockItemInterface::IS_QTY_DECIMAL] = (int)$stockItem->getIsQtyDecimal();
                         $result[StockItemInterface::IS_DECIMAL_DIVIDED]= (int)$stockItem->getIsDecimalDivided();
                         $result[StockItemInterface::BACKORDERS] = (int)$stockItem->getBackorders();
                         $result[StockItemInterface::NOTIFY_STOCK_QTY] = (float)$stockItem->getNotifyStockQty();
                         $result[StockItemInterface::ENABLE_QTY_INCREMENTS] = (int)$stockItem->getEnableQtyIncrements();
                         $result[StockItemInterface::QTY_INCREMENTS] = (float)$stockItem->getQtyIncrements();
                         $result[StockItemInterface::IS_IN_STOCK] = (int)$stockItem->getIsInStock();
                        */
                        // Preparing field values
                        if ($stockField == 'qty') {
                            if ($this->getConfigFlag('import_relative_stock_level')) {
                                // Check for relative updating
                                $tempValue = (string)$newValue;
                                if ($tempValue[0] == '+') {
                                    $newValue = $stockValue + substr($newValue, 1);
                                }
                                if ($tempValue[0] == '-') {
                                    $newValue = $stockValue - substr($newValue, 1);
                                }
                            }
                        }
                        if ($stockField == 'backorders' && $newValue !== '') {
                            if (isset($stockItem['use_config_backorders']) && $stockItem['use_config_backorders'] == 1) {
                                $updatedFields['use_config_backorders'] = 0;
                            }
                        }
                        // Compare and see if the value changed at all
                        if ($newValue !== $stockValue) {
                            if (trim($newValue) !== '') {
                                $updatedFields[$field] = $newValue;
                            }
                        }
                        // Uncomment this to *increment* stock levels by the imported QTY instead of replacing the stock level with the imported QTY.
                        /*
                        if ($stockField == 'qty') {
                            if ($stockValue <= 0) $stockValue = 0;
                            $updatedFields[$field] = $stockValue + $newValue;
                        }
                        */
                        break 1;
                    }
                }
            }
        }

        /*
         * Second run: See if we have to adjust values based on other field values.
         */
        foreach ($updatedFields as $field => $value) {
            if ($field == 'qty' && !isset($updateData['is_in_stock'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                if (!isset($stockItem) || (int)$stockItem['use_config_min_qty'] === 1) {
                    $outOfStockValue = (int)$this->scopeConfig->getValue('cataloginventory/item_options/min_qty');
                } else {
                    $outOfStockValue = $stockItem['min_qty'];
                }
                // Get "backorders allowed"
                if (!isset($stockItem) || (int)$stockItem['use_config_backorders'] === 1) {
                    $allowBackorders = (int)$this->scopeConfig->getValue('cataloginventory/item_options/backorders');
                } else {
                    $allowBackorders = $stockItem['backorders'];
                }
                /* $value = Stock level */
                if (!$allowBackorders && $this->getConfigFlag('mark_out_of_stock') && $value <= $outOfStockValue) {
                    $updatedFields['is_in_stock'] = 0;
                } else if ($this->getConfigFlag('mark_out_of_stock') && $value > 0) {
                    $updatedFields['is_in_stock'] = (int)($value > $outOfStockValue);
                }
                if ($allowBackorders && $this->getConfigFlag('mark_out_of_stock') && $value <= $outOfStockValue) {
                    // Debug message: Not setting to out of stock as its a backorderable item
                }
                #var_dump((int)$stockItem['use_config_min_qty'], $outOfStockValue, $updatedFields['is_in_stock']); die();
            }
            if ($field == 'backorders' && isset($updateData['backorders']) && $updateData['backorders'] !== '') {
                $updatedFields['use_config_backorders'] = 0;
            }
            if ($field == 'min_qty' && isset($updateData['min_qty']) && $updateData['min_qty'] !== '') {
                $updatedFields['use_config_min_qty'] = 0;
            }
            if ($field == 'min_sale_qty' && isset($updateData['min_sale_qty']) && $updateData['min_sale_qty'] !== '') {
                $updatedFields['use_config_min_sale_qty'] = 0;
            }
            if ($field == 'max_sale_qty' && isset($updateData['max_sale_qty']) && $updateData['max_sale_qty'] !== '') {
                $updatedFields['use_config_max_sale_qty'] = 0;
            }
        }

        return $updatedFields;
    }


    /*
     * See which fields in the MSI tables have changed
     */
    protected function getUpdatedMsiFields($updateData, $productSku)
    {
        $updatedFields = [];
        $productId = array_search($productSku, $this->productIdToSku);
        $productType = $this->productTypeMap[$productId];

        /*
         * First run: See which values changed, adjust values based on that and return fields to update.
         */
        if (!isset($this->msiItems[$updateData['source_code']]) || !isset($this->msiItems[$updateData['source_code']][$productSku])) {
            // New stock item
            foreach ($updateData as $field => $newValue) {
                if ($field == 'is_in_stock') {
                    $field = 'status'; // Called status in MSI tables
                    $newValue = (int)$newValue;
                    $updatedFields[$field] = $newValue;
                }
                if ($field == 'qty' && $productType !== Configurable::TYPE_CODE) { // Cannot update qty for configurable
                    $field = 'quantity'; // Called quantity in MSI tables
                    $newValue = (float)$newValue;
                    $updatedFields[$field] = $newValue;
                }
            }
        } else {
            // Already existing
            $msiItem = $this->msiItems[$updateData['source_code']][$productSku];
            foreach ($updateData as $field => $newValue) {
                $processField = false;
                if ($field == 'is_in_stock') {
                    $field = 'status'; // Called status in MSI tables
                    $processField = true;
                }
                if ($field == 'qty' && $productType !== Configurable::TYPE_CODE) { // Cannot update qty for configurable
                    $field = 'quantity'; // Called quantity in MSI tables
                    $processField = true;
                }
                if (!$processField) {
                    continue;
                }
                foreach ($msiItem as $stockField => $stockValue) {
                    if ($field == $stockField) {
                        // Type casting - everything coming from the database is a string (apparently, at least in my tests)
                        if ($stockField == 'status') {
                            $stockValue = (int)$stockValue; // Should be an integer coming from the database.
                        }
                        // Preparing field values
                        if ($stockField == 'quantity') {
                            if ($productType === Configurable::TYPE_CODE) { // Cannot update qty for configurable
                                continue;
                            }
                            if ($this->getConfigFlag('import_relative_stock_level')) {
                                // Check for relative updating
                                $tempValue = (string)$newValue;
                                if ($tempValue[0] == '+') {
                                    $newValue = $stockValue + substr($newValue, 1);
                                }
                                if ($tempValue[0] == '-') {
                                    $newValue = $stockValue - substr($newValue, 1);
                                }
                            }
                        }
                        // Compare and see if the value changed at all
                        if ($newValue !== $stockValue) {
                            if (trim($newValue) !== '') {
                                $updatedFields[$field] = $newValue;
                            }
                        }
                        // Uncomment this to *increment* stock levels by the imported QTY instead of replacing the stock level with the imported QTY.
                        /*
                        if ($stockField == 'qty') {
                            if ($stockValue <= 0) $stockValue = 0;
                            $updatedFields[$field] = $stockValue + $newValue;
                        }
                        */
                        break 1;
                    }
                }
            }
        }

        // Get stock item for SKU
        if ($productId !== false && isset($this->stockItems[1]) && isset($this->stockItems[1][$productId])) {
            $stockItem = $this->stockItems[1][$productId];
        }

        /*
         * Second run: See if we have to adjust values based on other field values.
         */
        foreach ($updatedFields as $field => $value) {
            if ($field == 'quantity' && !isset($updateData['status'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                if (!isset($stockItem) || (int)$stockItem['use_config_min_qty'] === 1) {
                    $outOfStockValue = (int)$this->scopeConfig->getValue('cataloginventory/item_options/min_qty');
                } else {
                    $outOfStockValue = $stockItem['min_qty'];
                }
                // Get "backorders allowed"
                if (!isset($stockItem) || (int)$stockItem['use_config_backorders'] === 1) {
                    $allowBackorders = (int)$this->scopeConfig->getValue('cataloginventory/item_options/backorders');
                } else {
                    $allowBackorders = $stockItem['backorders'];
                }
                /* $value = Stock level */
                if (!$allowBackorders && $this->getConfigFlag('mark_out_of_stock') && $value <= $outOfStockValue) {
                    $updatedFields['status'] = 0;
                } else if ($this->getConfigFlag('mark_out_of_stock') && $value > 0) {
                    $updatedFields['status'] = (int)($value > $outOfStockValue);
                }
                if ($allowBackorders && $this->getConfigFlag('mark_out_of_stock') && $value <= $outOfStockValue) {
                    // Debug message: Not setting to out of stock as its a backorderable item
                }
                #var_dump((int)$stockItem['use_config_min_qty'], $outOfStockValue, $updatedFields['is_in_stock']); die();
            }
        }

        return $updatedFields;
    }

    protected function insertStockItem($productId, $productIdentifier, $updateData)
    {
        // Some debugging information
        $tempUpdateData = $updateData;
        array_walk($tempUpdateData, function(&$i, $k) {
            $i = " \"$k\"=\"$i\"";
        });
        if ($this->getTestMode()) {
            $importResult = ['changed' => true, 'debug' => __("Product '%1' (New stock item) would have been imported into Magento. Identified product ID is %2. New fields: %3", $productIdentifier, $productId, implode("", $tempUpdateData))];
            return $importResult;
        }

        // Prepare the stock_item and insert it
        if (isset($updateData['qty'])) {
            if (!isset($updateData['is_in_stock'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                $outOfStockValue = (int)$this->scopeConfig->getValue('cataloginventory/item_options/min_qty');
                if ($this->getConfigFlag('mark_out_of_stock') && $updateData['qty'] <= $outOfStockValue) {
                    $updateData['is_in_stock'] = 0;
                } else if ($this->getConfigFlag('mark_out_of_stock') && $updateData['qty'] > 0) {
                    $updateData['is_in_stock'] = (int)($updateData['qty'] > $outOfStockValue);
                }
            }
        }
        #$updateData['stock_id'] = 1;
        $updateData['product_id'] = $productId;

        $updatedFields = $updateData;
        foreach ($updatedFields as $field => $value) {
            if ($field != 'is_in_stock' && $field != 'qty' && $field != 'stock_id' && $field != 'product_id') { // Do not process these here.
                unset($updatedFields[$field]);
            }
        }
        $this->writeAdapter->insert($this->getTableName('cataloginventory_stock_item'), $updatedFields);

        // Import result
        $importResult = ['changed' => true, 'debug' => __("Product '%1' (New stock item) has been imported into Magento. Identified product ID is %2. New fields: %3", $productIdentifier, $productId, implode("", $tempUpdateData))];
        return $importResult;
    }

    protected function insertStockStatus($productId, $updatedFields)
    {
        // Entry in stock_status does not exist yet, insert it
        #$updateData['stock_id'] = 1;
        $updateData['product_id'] = $productId;
        if (isset($updatedFields['is_in_stock'])) {
            $updateData['stock_status'] = $updatedFields['is_in_stock'];
        }
        if (isset($updatedFields['qty'])) {
            if (!isset($updateData['stock_status'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                $outOfStockValue = (int)$this->scopeConfig->getValue('cataloginventory/item_options/min_qty');
                if ($this->getConfigFlag('mark_out_of_stock') && $updatedFields['qty'] <= $outOfStockValue) {
                    $updateData['stock_status'] = 0;
                } else if ($this->getConfigFlag('mark_out_of_stock') && $updatedFields['qty'] > 0) {
                    $updateData['stock_status'] = (int)($updatedFields['qty'] > $outOfStockValue);
                }
            }
            $updateData['qty'] = $updatedFields['qty'];
        }
        if (isset($updatedFields['stock_id'])) {
            $updateData['stock_id'] = $updatedFields['stock_id'];
        }

        //foreach ($this->storeManager->getWebsites() as $website) {
        $updateData['website_id'] = 0; // StockConfigurationInterface -> getDefaultScopeId()
        $this->writeAdapter->insert($this->getTableName('cataloginventory_stock_status'), $updateData);
        //}

        return $this;
    }

    protected function insertMsiItem($productSku, $updatedFields)
    {
        // Entry in MSI table does not exist yet, insert it
        $insertData = [
            'source_code' => $updatedFields['source_code'],
            'sku' => $productSku
        ];

        if (isset($updatedFields['is_in_stock'])) {
            $insertData['status'] = $updatedFields['is_in_stock'];
        }
        if (isset($updatedFields['qty'])) {
            if (!isset($insertData['status'])) {
                // Update is_in_stock field, only if not set in import file and if config flag mark_out_of_stock is set to yes
                $outOfStockValue = (int)$this->scopeConfig->getValue('cataloginventory/item_options/min_qty');
                if ($this->getConfigFlag('mark_out_of_stock') && $updatedFields['qty'] <= $outOfStockValue) {
                    $insertData['status'] = 0;
                } else if ($this->getConfigFlag('mark_out_of_stock') && $updatedFields['qty'] > 0) {
                    $insertData['status'] = (int)($updatedFields['qty'] > $outOfStockValue);
                }
            }
            $insertData['quantity'] = $updatedFields['qty'];
        }

        $this->writeAdapter->insert($this->getTableName('inventory_source_item'), $insertData);
        return $this;
    }

    protected function updateStockItem($productId, $productIdentifier, $updatedFields, $updateData)
    {
        // Some debugging information
        $tempUpdatedFields = $updatedFields;
        array_walk($tempUpdatedFields, function(&$i, $k) {
            $i = " \"$k\"=\"$i\"";
        });
        if ($this->getTestMode()) {
            // Don't touch the stock item. Just return some fancy debug information.
            $importResult = ['changed' => true, 'debug' => __("Product '%1' would have been imported into Magento. Identified product ID is %2. Updated fields: %3", $productIdentifier, $productId, implode("", $tempUpdatedFields))];
            return $importResult;
        }

        // Update stock_item
        $this->writeAdapter->update($this->getTableName('cataloginventory_stock_item'), $updatedFields, "product_id=$productId and stock_id=" . $updateData['stock_id']);

        // Import result
        $importResult = ['changed' => true, 'debug' => __("Product '%1' has been imported into Magento. Identified product ID is %2. Updated fields: %3", $productIdentifier, $productId, implode("", $tempUpdatedFields))];
        return $importResult;
    }

    protected function updateStockStatus($productId, $updatedFields, $updateData)
    {
        // Entry in stock_status already exists, update it
        $statusUpdate = [];
        if (isset($updatedFields['qty'])) {
            $statusUpdate['qty'] = $updatedFields['qty'];
        }
        if (isset($updatedFields['is_in_stock'])) {
            $statusUpdate['stock_status'] = $updatedFields['is_in_stock'];
        }

        // Update it only if something has changed which is interesting for the stock_status
        if (!empty($statusUpdate)) {
            $this->writeAdapter->update($this->getTableName('cataloginventory_stock_status'), $statusUpdate, "product_id=$productId and stock_id=" . $updateData['stock_id']); // . " and website_id=1");
        }

        return $this;
    }

    protected function updateMsiItem($productSku, $updatedFields, $updateData)
    {
        // Entry in MSI table already exists, update it
        $msiData = [];
        if (isset($updatedFields['quantity'])) {
            $msiData['quantity'] = $updatedFields['quantity'];
        }
        if (isset($updatedFields['status'])) {
            $msiData['status'] = $updatedFields['status'];
        }

        // Update MSI table
        $this->writeAdapter->update($this->getTableName('inventory_source_item'), $msiData, "sku=".$this->writeAdapter->quote($productSku)." and source_code=".$this->writeAdapter->quote($updateData['source_code'])."");

        return $this;
    }

    /*
     * After the import ran, currently the only thing done is committing the transaction and reindexing
     */
    public function afterRun()
    {
        // Commit the transaction, only if no product data is updated
        if (!self::$importPrices && !self::$importSpecialPrices && !self::$importCost && !self::$importProductStatus && !self::$importCustomAttributes) {
            $this->writeAdapter->commit();
        }
        #$this->writeAdapter->query('UNLOCK TABLES');

        // Reindex routine
        if ($this->getTestMode()) {
            $this->getLogEntry()->addDebugMessage(__('Test mode enabled. Not running any reindex action.'));
            return $this;
        }
        try {
            // MSI Reindex, if required
            if (!empty($this->modifiedMsiItems) && !$this->getConfigFlag('disable_msi_reindex')) {
                $this->getLogEntry()->addDebugMessage(__('Starting MSI reindex.'));
                $startTime = microtime(true);
                $indexer = $this->indexerRegistry->get('inventory');
                if (!$indexer->isWorking()) {
                    $indexer->reindexAll();
                }
                $this->getLogEntry()->addDebugMessage(__('MSI reindex completed in %1 seconds.', round(microtime(true) - $startTime)));
            }
            if (!empty($this->modifiedStockItems) || !empty($this->updatedCustomProductAttributes)) {
                if ($this->getConfigFlag('invalidate_fpc')) {
                    $flushCacheObserver = $this->objectManager->create('\Magento\PageCache\Observer\FlushCacheByTags');
                    //$flushVarnishObserver = $this->objectManager->create('\Magento\CacheInvalidate\Observer\InvalidateVarnishObserver');
                    // Full page cache invalidation - not possible without loading the collection unfortunately
                    $productIds = array_unique(array_merge($this->modifiedStockItems, $this->updatedCustomProductAttributes));
                    // Parent products
                    $select = $this->readAdapter->select()
                        ->from(['l' => $this->getTableName('catalog_product_super_link')], [])
                        ->join(
                            ['e' => $this->getTableName('catalog_product_entity')],
                            'e.' . $this->objectManager->get('Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionProvider')->getProductEntityLinkField() . ' = l.parent_id',
                            ['e.entity_id']
                        )->where('l.product_id IN(?)', $productIds)->group('e.entity_id');
                    $parentProductIds = $this->readAdapter->fetchCol($select);
                    foreach ($parentProductIds as $parentProductId) {
                        //\Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Cache\Type\Collection')->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['configurable_' . $parentProductId]);
                        $this->cacheManager->clean('catalog_product_' . $parentProductId);
                        $this->cacheManager->clean('cat_p_' . $parentProductId);
                    }
                    //$productIds = array_unique(array_merge($productIds, $parentProductIds)); // Also load parent product IDs - NOT required, flush cache observer getTags gets them automatically
                    $productCollection = $this->objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection');
                    $productCollection->addFieldToFilter('entity_id', ['in' => $productIds]);
                    foreach ($productCollection as $product) {
                        $this->cacheManager->clean('catalog_product_' . $product->getId());
                        $this->cacheManager->clean('cat_p_' . $product->getId());
                        $product->cleanCache();
                        // Trigger FPC observer
                        $observer = new \Magento\Framework\Event\Observer(
                            [
                                'event' => new \Magento\Framework\DataObject(
                                    [
                                        'object' => $product
                                    ]
                                )
                            ]
                        );
                        $flushCacheObserver->execute($observer);
                        //$flushVarnishObserver->execute($observer);
                    }
                }
                if ($this->getConfigFlag('update_parent_product_stock_after_import')) {
                    // Update all configurable products to in stock/out of stock based on the qty of their child products
                    // Magento MSI Variant:  Update all configurable products to in stock/out of stock based on the qty of their child products
                    if ($this->entityHelper->getMagentoMSISupport()) {
                        $configurableProducts = $this->productFactory->create()->getCollection()
                            ->addAttributeToFilter('type_id', Configurable::TYPE_CODE);
                        foreach ($configurableProducts as $configurableProduct) {
                            $isInStock = false;
                            $childProducts = $this->configurableProduct->getUsedProducts($configurableProduct);
                            foreach ($childProducts as $childProduct) {
                                $childStockItem = $this->stockRegistry->getStockItem($childProduct->getId());
                                if (!$childStockItem->getUseConfigMinQty()) {
                                    $minQty = $childStockItem->getMinQty();
                                } else {
                                    $minQty = (int)$this->scopeConfig->getValue('cataloginventory/item_options/min_qty');
                                }
                                try {
                                    $stockInfos = $this->objectManager->get('Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku')->execute($childProduct->getSku());
                                    foreach ($stockInfos as $stockInfo) {
                                        if ($stockInfo['qty'] > $minQty) {
                                            $isInStock = true;
                                            break 2;
                                        }
                                    }
                                } catch (\Exception $e) {
                                }
                            }
                            $this->writeAdapter->update($this->getTableName('cataloginventory_stock_item'), ['is_in_stock' => (int)$isInStock], "product_id=" . $configurableProduct->getId());
                        }
                    } else {
                        $configurableProducts = $this->productFactory->create()->getCollection()
                            ->addAttributeToFilter('type_id', Configurable::TYPE_CODE);
                        foreach ($configurableProducts as $configurableProduct) {
                            $isInStock = false;
                            $childProducts = $this->configurableProduct->getUsedProducts($configurableProduct);
                            foreach ($childProducts as $childProduct) {
                                $childStockItem = $this->stockRegistry->getStockItem($childProduct->getId());
                                if ($childStockItem->getIsInStock()) {
                                    $isInStock = true;
                                    break 1;
                                }
                            }
                            $this->writeAdapter->update($this->getTableName('cataloginventory_stock_item'), ['is_in_stock' => (int)$isInStock], "product_id=" . $configurableProduct->getId());
                        }
                    }
                }
                // Get "configurable products" and update all associated child products to qty of parent item
                /*if (!empty($this->productTypeMap)) {
                    foreach ($this->productTypeMap as $parentProductId => $productType) {
                        if ($productType == Configurable::TYPE_CODE) {
                            $parentProduct = $this->productFactory->create()->load($parentProductId);
                            $parentStockItem = $this->stockRegistry->getStockItem($parentProductId);
                            if ($parentStockItem->getId()) {
                                $childProducts = $this->configurableProduct->getUsedProducts($parentProduct);
                                foreach ($childProducts as $childProduct) {
                                    $childStockItem = $this->stockRegistry->getStockItem($childProduct->getId());
                                    if ($parentStockItem->getQty() !== $childStockItem->getQty()) {
                                        $childStockItem->setQty($parentStockItem->getQty())->save();
                                    }
                                }
                            }
                        }
                    }
                }*/
                // Check if M2ePro is installed, and if yes, update stock there:
                if ($this->utilsHelper->isExtensionInstalled('Ess_M2ePro')) {
                    $model = $this->objectManager->create('\Ess\M2ePro\PublicServices\Product\SqlChange');
                    if ($model !== false) {
                        // Stock level
                        if (!empty($this->modifiedStockItems)) {
                            foreach ($this->modifiedStockItems as $productId) {
                                $model->markQtyWasChanged($productId);
                            }
                        }
                        // Price
                        $priceUpdateProductIds = array_unique(array_merge($this->updatedPrices, $this->updatedTierPrices, $this->updatedSpecialPrices));
                        if (!empty($priceUpdateProductIds)) {
                            foreach ($priceUpdateProductIds as $productId) {
                                $model->markPriceWasChanged($productId);
                            }
                        }
                        // Product Status
                        if (!empty($this->updatedProductStatuses)) {
                            foreach ($this->updatedProductStatuses as $productId) {
                                $model->markStatusWasChanged($productId);
                            }
                        }
                        $model->applyChanges();
                        $this->getLogEntry()->addDebugMessage(__('Notified M2ePro about updated products/stock levels.'));
                    }
                }
                // Webkul eBay Connector support, do not call $product->save but instead just call their observer (but unfortunately we need to load each product...)
                if ($this->utilsHelper->isExtensionInstalled('Webkul_Ebaymagentoconnect')) {
                    foreach ($this->modifiedStockItems as $productId) {
                        $observer = $this->objectManager->create('\Webkul\Ebaymagentoconnect\Observer\CatalogProductSaveAfter');
                        $product = $this->objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
                        $observer->execute(new \Magento\Framework\Event\Observer(['product' => $product]));
                        unset($product);
                    }
                }
                // Reindex, if required
                if ($this->getConfig('reindex_mode') == 'full') {
                    // Reindex - required for sure if using MSI
                    $this->getLogEntry()->addDebugMessage(__('Running reindex.'));
                    $startTime = microtime(true);
                    $indexer = $this->indexerRegistry->get(\Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID);
                    if (!$indexer->isWorking()) {
                        $indexer->reindexAll();
                    }
                    $this->getLogEntry()->addDebugMessage(__('Reindex completed in %1 seconds.', round(microtime(true) - $startTime)));
                } else if ($this->getConfig('reindex_mode') == 'flag_index') {
                    $this->getLogEntry()->addDebugMessage(__('Flagging stock index as reindex required.'));
                    // Flag as reindex required
                    $indexer = $this->indexerRegistry->get(\Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID);
                    if (!$indexer->isWorking()) {
                        $indexer->getState()->setStatus(\Magento\Framework\Indexer\StateInterface::STATUS_INVALID);
                    }
                } else if ($this->getConfig('reindex_mode') == 'no_reindex') {
                    $this->getLogEntry()->addDebugMessage(__('Reindexing disabled. Not touching index at all.'));
                }
            } else {
                $this->getLogEntry()->addDebugMessage(__('No stock items modified. No reindex actions required.'));
            }

            // Refresh Magento Enterprise Edition Full Page Cache
            if ($this->getConfig('enterprise_fpc_action') == 'invalidate') {
                $this->getLogEntry()->addDebugMessage(__('Invalidating Magento Enterprise Full Page Cache.'));
                $this->cacheTypeList->invalidate('full_page');
            } else if ($this->getConfig('enterprise_fpc_action') == 'clean') {
                $this->getLogEntry()->addDebugMessage(__('Cleaning Magento Enterprise Full Page Cache.'));
                $this->pageCache->clean();
            }

            if ($this->getConfigFlag('update_low_stock_date')) {
                // Refresh "Low stock date"
                $this->resourceStock->updateLowStockDate(true);
            }

            // Reindex for price updates
            if (self::$importPrices || self::$importSpecialPrices || self::$importCustomAttributes) {
                if (!empty($this->updatedPrices) || !empty($this->updatedTierPrices) || !empty($this->updatedSpecialPrices) || !empty($this->updatedProductStatuses) || !empty($this->updatedCustomProductAttributes)) {
                    $this->getLogEntry()->addDebugMessage(__('Price/attribute update: Running reindex for price, product_flat and category_product.'));
                    $startTime = microtime(true);
                    $indexers = [
                        \Magento\Catalog\Model\Indexer\Product\Price\Processor::INDEXER_ID,
                        \Magento\Catalog\Model\Indexer\Product\Flat\Processor::INDEXER_ID,
                        \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID
                    ];
                    foreach ($indexers as $indexerId) {
                        try {
                            $indexer = $this->indexerRegistry->get($indexerId);
                        } catch (\InvalidArgumentException $e) {
                            continue; // Flat indexer doesn't exist if disabled
                        }
                        if (!$indexer->isWorking()) {
                            try {
                                $indexer->reindexAll();
                            } catch (\Exception $e) {
                                if ($e->getMessage() != 'No linked stock found') { // MSI Error
                                    $this->getLogEntry()->addDebugMessage(__('Error while reindexing %1. Exception: %2', $indexerId, $e->getMessage()));
                                }
                            }
                        }
                    }
                    $this->getLogEntry()->addDebugMessage(
                        __(
                            'Price/attribute update: Full reindex completed in %1 seconds.',
                            round(microtime(true) - $startTime)
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            $this->getLogEntry()->addDebugMessage(__('General error while reindexing. Exception: %1', $e->getMessage()));
        }

        $this->eventManager->dispatch('xtento_stockimport_stockupdate_after', [
            'profile' => $this->getProfile(),
            'log' => $this->getLogEntry(),
            'modified_stock_items' => $this->modifiedStockItems // Array containing the product IDs updated
        ]);

        // End of reindexing routine
        $this->getLogEntry()->addDebugMessage(__('Done: afterRun() (Reindexer functions, ...)'));
    }
}