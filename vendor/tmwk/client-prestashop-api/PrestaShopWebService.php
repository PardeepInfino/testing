<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 8:22
 */

namespace TMWK\ClientPrestashopApi;

use TMWK\ClientPrestashopApi\Lib\Addresses;
use TMWK\ClientPrestashopApi\Lib\Carriers;
use TMWK\ClientPrestashopApi\Lib\CartRules;
use TMWK\ClientPrestashopApi\Lib\Carts;
use TMWK\ClientPrestashopApi\Lib\Categories;
use TMWK\ClientPrestashopApi\Lib\Combinations;
use TMWK\ClientPrestashopApi\Lib\Configurations;
use TMWK\ClientPrestashopApi\Lib\Contacts;
use TMWK\ClientPrestashopApi\Lib\ContentManagementSystem;
use TMWK\ClientPrestashopApi\Lib\Countries;
use TMWK\ClientPrestashopApi\Lib\Currencies;
use TMWK\ClientPrestashopApi\Lib\CustomerMessages;
use TMWK\ClientPrestashopApi\Lib\Customers;
use TMWK\ClientPrestashopApi\Lib\CustomerThreads;
use TMWK\ClientPrestashopApi\Lib\Deliveries;
use TMWK\ClientPrestashopApi\Lib\Employees;
use TMWK\ClientPrestashopApi\Lib\Groups;
use TMWK\ClientPrestashopApi\Lib\Guests;
use TMWK\ClientPrestashopApi\Lib\Images;
use TMWK\ClientPrestashopApi\Lib\ImageTypes;
use TMWK\ClientPrestashopApi\Lib\Languages;
use TMWK\ClientPrestashopApi\Lib\Manufacturers;
use TMWK\ClientPrestashopApi\Lib\OrderCarriers;
use TMWK\ClientPrestashopApi\Lib\OrderDetails;
use TMWK\ClientPrestashopApi\Lib\OrderDiscounts;
use TMWK\ClientPrestashopApi\Lib\OrderHistories;
use TMWK\ClientPrestashopApi\Lib\OrderInvoices;
use TMWK\ClientPrestashopApi\Lib\OrderPayments;
use TMWK\ClientPrestashopApi\Lib\Orders;
use TMWK\ClientPrestashopApi\Lib\OrderStates;
use TMWK\ClientPrestashopApi\Lib\PriceRanges;
use TMWK\ClientPrestashopApi\Lib\ProductFeatures;
use TMWK\ClientPrestashopApi\Lib\ProductFeatureValues;
use TMWK\ClientPrestashopApi\Lib\ProductOptions;
use TMWK\ClientPrestashopApi\Lib\ProductOptionValues;
use TMWK\ClientPrestashopApi\Lib\Products;
use TMWK\ClientPrestashopApi\Lib\ProductSuppliers;
use TMWK\ClientPrestashopApi\Lib\Search;
use TMWK\ClientPrestashopApi\Lib\ShopGroups;
use TMWK\ClientPrestashopApi\Lib\Shops;
use TMWK\ClientPrestashopApi\Lib\SpecificPriceRules;
use TMWK\ClientPrestashopApi\Lib\SpecificPrices;
use TMWK\ClientPrestashopApi\Lib\States;
use TMWK\ClientPrestashopApi\Lib\StockAvailables;
use TMWK\ClientPrestashopApi\Lib\StockMovementReasons;
use TMWK\ClientPrestashopApi\Lib\StockMovements;
use TMWK\ClientPrestashopApi\Lib\Stocks;
use TMWK\ClientPrestashopApi\Lib\Stores;
use TMWK\ClientPrestashopApi\Lib\Suppliers;
use TMWK\ClientPrestashopApi\Lib\SupplyOrderDetails;
use TMWK\ClientPrestashopApi\Lib\SupplyOrderHistories;
use TMWK\ClientPrestashopApi\Lib\SupplyOrderReceiptHistories;
use TMWK\ClientPrestashopApi\Lib\SupplyOrders;
use TMWK\ClientPrestashopApi\Lib\SupplyOrderStates;
use TMWK\ClientPrestashopApi\Lib\Tags;
use TMWK\ClientPrestashopApi\Lib\Taxes;
use TMWK\ClientPrestashopApi\Lib\TaxRuleGroups;
use TMWK\ClientPrestashopApi\Lib\TaxRules;
use TMWK\ClientPrestashopApi\Lib\TranslatedConfigurations;
use TMWK\ClientPrestashopApi\Lib\WarehouseProductLocations;
use TMWK\ClientPrestashopApi\Lib\Warehouses;
use TMWK\ClientPrestashopApi\Lib\WeightRanges;
use TMWK\ClientPrestashopApi\Lib\Zones;

class PrestaShopWebService
{
    private static $_config;

    /**
     * PrestaShopWebService constructor.
     * @param $ps_url
     * @param $ps_key
     * @param $ps_debug
     */
    public function __construct($ps_url, $ps_key, $ps_debug)
    {
        $config = new Config();
        $config::setUrl($ps_url);
        $config::setKey($ps_key);
        $config::setDebug($ps_debug);
        self::$_config = $config;
    }

    /**
     * @return Addresses
     */
    public function Addresses()
    {
        return new Addresses();
    }

    /**
     * @return Carriers
     */
    public function Carriers()
    {
        return new Carriers();
    }

    /**
     * @return CartRules
     */
    public function CartRules()
    {
        return new CartRules();
    }

    /**
     * @return Carts
     */
    public function Carts()
    {
        return new Carts();
    }

    /**
     * @return Categories
     */
    public function Categories()
    {
        return new Categories();
    }

    /**
     * @return Combinations
     */
    public function Combinations()
    {
        return new Combinations();
    }

    /**
     * @return Configurations
     */
    public function Configurations()
    {
        return new Configurations();
    }

    /**
     * @return Contacts
     */
    public function Contacts()
    {
        return new Contacts();
    }

    /**
     * @return ContentManagementSystem
     */
    public function ContentManagementSystem()
    {
        return new ContentManagementSystem();
    }

    /**
     * @return Countries
     */
    public function Countries()
    {
        return new Countries();
    }

    /**
     * @return Currencies
     */
    public function Currencies()
    {
        return new Currencies();
    }

    /**
     * @return CustomerMessages
     */
    public function CustomerMessages()
    {
        return new CustomerMessages();
    }

    /**
     * @return Customers
     */
    public function Customers()
    {
        return new Customers();
    }

    /**
     * @return CustomerThreads
     */
    public function CustomerThreads()
    {
        return new CustomerThreads();
    }

    /**
     * @return Deliveries
     */
    public function Deliveries()
    {
        return new Deliveries();
    }

    /**
     * @return Employees
     */
    public function Employees()
    {
        return new Employees();
    }

    /**
     * @return Groups
     */
    public function Groups()
    {
        return new Groups();
    }

    /**
     * @return Guests
     */
    public function Guests()
    {
        return new Guests();
    }

    /**
     * @return Images
     */
    public function Images()
    {
        return new Images();
    }

    /**
     * @return ImageTypes
     */
    public function ImageTypes()
    {
        return new ImageTypes();
    }

    /**
     * @return Languages
     */
    public function Languages()
    {
        return new Languages();
    }

    /**
     * @return Manufacturers
     */
    public function Manufacturers()
    {
        return new Manufacturers();
    }

    /**
     * @return OrderCarriers
     */
    public function OrderCarriers()
    {
        return new OrderCarriers();
    }

    /**
     * @return OrderDetails
     */
    public function OrderDetails()
    {
        return new OrderDetails();
    }

    /**
     * @return OrderDiscounts
     */
    public function OrderDiscounts()
    {
        return new OrderDiscounts();
    }

    /**
     * @return OrderHistories
     */
    public function OrderHistories()
    {
        return new OrderHistories();
    }

    /**
     * @return OrderInvoices
     */
    public function OrderInvoices()
    {
        return new OrderInvoices();
    }

    /**
     * @return OrderPayments
     */
    public function OrderPayments()
    {
        return new OrderPayments();
    }

    /**
     * @return Orders
     */
    public function Orders()
    {
        return new Orders();
    }

    /**
     * @return OrderStates
     */
    public function OrderStates()
    {
        return new OrderStates();
    }

    /**
     * @return PriceRanges
     */
    public function PriceRanges()
    {
        return new PriceRanges();
    }

    /**
     * @return ProductFeatures
     */
    public function ProductFeatures()
    {
        return new ProductFeatures();
    }

    /**
     * @return ProductFeatureValues
     */
    public function ProductFeatureValues()
    {
        return new ProductFeatureValues();
    }

    /**
     * @return ProductOptions
     */
    public function ProductOptions()
    {
        return new ProductOptions();
    }

    /**
     * @return ProductOptionValues
     */
    public function ProductOptionValues()
    {
        return new ProductOptionValues();
    }

    /**
     * @return Products
     */
    public function Products()
    {
        return new Products();
    }

    /**
     * @return ProductSuppliers
     */
    public function ProductSuppliers()
    {
        return new ProductSuppliers();
    }

    /**
     * @return Search
     */
    public function Search()
    {
        return new Search();
    }

    /**
     * @return ShopGroups
     */
    public function ShopGroups()
    {
        return new ShopGroups();
    }

    /**
     * @return Shops
     */
    public function Shops()
    {
        return new Shops();
    }

    /**
     * @return SpecificPriceRules
     */
    public function SpecificPriceRules()
    {
        return new SpecificPriceRules();
    }

    /**
     * @return SpecificPrices
     */
    public function SpecificPrices()
    {
        return new SpecificPrices();
    }

    /**
     * @return States
     */
    public function States()
    {
        return new States();
    }

    /**
     * @return StockAvailables
     */
    public function StockAvailables()
    {
        return new StockAvailables();
    }

    /**
     * @return StockMovementReasons
     */
    public function StockMovementReasons()
    {
        return new StockMovementReasons();
    }

    /**
     * @return StockMovements
     */
    public function StockMovements()
    {
        return new StockMovements();
    }

    /**
     * @return Stocks
     */
    public function Stocks()
    {
        return new Stocks();
    }

    /**
     * @return Stores
     */
    public function Stores()
    {
        return new Stores();
    }

    /**
     * @return Suppliers
     */
    public function Suppliers()
    {
        return new Suppliers();
    }

    /**
     * @return SupplyOrderDetails
     */
    public function SupplyOrderDetails()
    {
        return new SupplyOrderDetails();
    }

    /**
     * @return SupplyOrderHistories
     */
    public function SupplyOrderHistories()
    {
        return new SupplyOrderHistories();
    }

    /**
     * @return SupplyOrderReceiptHistories
     */
    public function SupplyOrderReceiptHistories()
    {
        return new SupplyOrderReceiptHistories();
    }

    /**
     * @return SupplyOrders
     */
    public function SupplyOrders()
    {
        return new SupplyOrders();
    }

    /**
     * @return SupplyOrderStates
     */
    public function SupplyOrderStates()
    {
        return new SupplyOrderStates();
    }

    /**
     * @return Tags
     */
    public function Tags()
    {
        return new Tags();
    }

    /**
     * @return Taxes
     */
    public function Taxes()
    {
        return new Taxes();
    }

    /**
     * @return TaxRuleGroups
     */
    public function TaxRuleGroups()
    {
        return new TaxRuleGroups();
    }

    /**
     * @return TaxRules
     */
    public function TaxRules()
    {
        return new TaxRules();
    }

    /**
     * @return TranslatedConfigurations
     */
    public function TranslatedConfigurations()
    {
        return new TranslatedConfigurations();
    }

    /**
     * @return WarehouseProductLocations
     */
    public function WarehouseProductLocations()
    {
        return new WarehouseProductLocations();
    }

    /**
     * @return Warehouses
     */
    public function Warehouses()
    {
        return new Warehouses();
    }

    /**
     * @return WeightRanges
     */
    public function WeightRanges()
    {
        return new WeightRanges();
    }

    /**
     * @return Zones
     */
    public function Zones()
    {
        return new Zones();
    }

}