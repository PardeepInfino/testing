<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 17/01/2018
 * Time: 9:12
 */

namespace TMWK\ClientPrestashopApi\Entities;


use TMWK\ClientPrestashopApi\Config;
use TMWK\ClientPrestashopApi\PrestaShopWebService;

class SpecificPrices
{
    private $id = null;

    private $id_shop_group = 0;

    private $id_shop = 0;

    private $id_cart = 0;

    private $id_product;

    private $id_product_attribute = 0;

    private $id_currency = 0;

    private $id_country = 0;

    private $id_group = 0;

    private $id_customer = 0;

    private $id_specific_price_rule = 0;

    private $price;

    private $from_quantity = 0;

    private $reduction = 0;

    private $reduction_tax = 0;

    private $reduction_type;

    private $from = '0000-00-00 00:00:00';

    private $to = '0000-00-00 00:00:00';

    public function __construct($id = null)
    {
        if ($id !== null) {
            $es = new PrestaShopWebService(Config::getUrl(), Config::getKey(), Config::getDebug());

            $entity = $es->SpecificPrices()->find($id);

            $this->id                     = $entity->specific_price->id;
            $this->id_shop_group          = $entity->specific_price->id_shop_group;
            $this->id_shop                = $entity->specific_price->id_shop;
            $this->id_cart                = $entity->specific_price->id_cart;
            $this->id_product             = $entity->specific_price->id_product;
            $this->id_product_attribute   = $entity->specific_price->id_product_attribute;
            $this->id_currency            = $entity->specific_price->id_currency;
            $this->id_country             = $entity->specific_price->id_country;
            $this->id_group               = $entity->specific_price->id_group;
            $this->id_customer            = $entity->specific_price->id_customer;
            $this->id_specific_price_rule = $entity->specific_price->id_specific_price_rule;
            $this->price                  = $entity->specific_price->price;
            $this->from_quantity          = $entity->specific_price->from_quantity;
            $this->reduction              = $entity->specific_price->reduction;
            $this->reduction_tax          = $entity->specific_price->reduction_tax;
            $this->reduction_type         = $entity->specific_price->reduction_type;
            $this->from                   = $entity->specific_price->from;
            $this->to                     = $entity->specific_price->to;
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $id_shop_group
     */
    public function setIdShopGroup($id_shop_group)
    {
        $this->id_shop_group = $id_shop_group;
    }

    /**
     * @param mixed $id_shop
     */
    public function setIdShop($id_shop)
    {
        $this->id_shop = $id_shop;
    }

    /**
     * @param mixed $id_cart
     */
    public function setIdCart($id_cart)
    {
        $this->id_cart = $id_cart;
    }

    /**
     * @param mixed $id_product
     */
    public function setIdProduct($id_product)
    {
        $this->id_product = $id_product;
    }

    /**
     * @param mixed $id_product_attribute
     */
    public function setIdProductAttribute($id_product_attribute)
    {
        $this->id_product_attribute = $id_product_attribute;
    }

    /**
     * @param mixed $id_currency
     */
    public function setIdCurrency($id_currency)
    {
        $this->id_currency = $id_currency;
    }

    /**
     * @param mixed $id_country
     */
    public function setIdCountry($id_country)
    {
        $this->id_country = $id_country;
    }

    /**
     * @param mixed $id_group
     */
    public function setIdGroup($id_group)
    {
        $this->id_group = $id_group;
    }

    /**
     * @param mixed $id_customer
     */
    public function setIdCustomer($id_customer)
    {
        $this->id_customer = $id_customer;
    }

    /**
     * @param mixed $id_specific_price_rule
     */
    public function setIdSpecificPriceRule($id_specific_price_rule)
    {
        $this->id_specific_price_rule = $id_specific_price_rule;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param mixed $from_quantity
     */
    public function setFromQuantity($from_quantity)
    {
        $this->from_quantity = $from_quantity;
    }

    /**
     * @param mixed $reduction
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;
    }

    /**
     * @param mixed $reduction_tax
     */
    public function setReductionTax($reduction_tax)
    {
        $this->reduction_tax = $reduction_tax;
    }

    /**
     * @param mixed $reduction_type
     */
    public function setReductionType($reduction_type)
    {
        $this->reduction_type = $reduction_type;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return array(
            'id'                     => $this->id,
            'id_shop_group'          => $this->id_shop_group,
            'id_shop'                => $this->id_shop,
            'id_cart'                => $this->id_cart,
            'id_product'             => $this->id_product,
            'id_product_attribute'   => $this->id_product_attribute,
            'id_currency'            => $this->id_currency,
            'id_country'             => $this->id_country,
            'id_group'               => $this->id_group,
            'id_customer'            => $this->id_customer,
            'id_specific_price_rule' => $this->id_specific_price_rule,
            'price'                  => $this->price,
            'from_quantity'          => $this->from_quantity,
            'reduction'              => $this->reduction,
            'reduction_tax'          => $this->reduction_tax,
            'reduction_type'         => $this->reduction_type,
            'from'                   => $this->from,
            'to'                     => $this->to
        );
    }
}