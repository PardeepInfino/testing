<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 9:06
 */

namespace TMWK\ClientPrestashopApi\Doc;


class ProductDetails
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $id_manufacturer;

    /**
     * @var integer
     */
    public $id_supplier;

    /**
     * @var integer
     */
    public $id_category_default;

    /**
     * @var boolean
     */
    public $new;

    /**
     * @var boolean
     */
    public $cache_default_attribute;

    /**
     * @var integer
     */
    public $id_default_image;

    /**
     * @var integer
     */
    public $id_default_combination;

    /**
     * @var integer
     */
    public $id_tax_rules_group;

    /**
     * @var integer
     */
    public $position_in_category;

    /**
     * @var string
     */
    public $manufacturer_name;

    /**
     * @var integer
     */
    public $quantity;

    /**
     * @var integer
     */
    public $type;

    /**
     * @var integer
     */
    public $id_shop_default;

    /**
     * @var string
     */
    public $reference;

    /**
     * @var string
     */
    public $supplier_reference;

    /**
     * @var mixed
     */
    public $location;

    /**
     * @var float
     */
    public $width;

    /**
     * @var float
     */
    public $height;

    /**
     * @var float
     */
    public $depth;

    /**
     * @var float
     */
    public $weight;

    /**
     * @var integer
     */
    public $quantity_discount;

    /**
     * @var mixed
     */
    public $ean13;

    /**
     * @var mixed
     */
    public $isbn;

    /**
     * @var mixed
     */
    public $upc;

    /**
     * @var boolean
     */
    public $cache_is_pack;

    /**
     * @var boolean
     */
    public $cache_has_attachments;

    /**
     * @var boolean
     */
    public $is_virtual;

    /**
     * @var boolean
     */
    public $state;

    /**
     * @var boolean
     */
    public $on_sale;

    /**
     * @var boolean
     */
    public $online_only;

    /**
     * @var float
     */
    public $ecotax;

    /**
     * @var boolean
     */
    public $minimal_quantity;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $wholesale_price;

    /**
     * @var
     */
    public $unity;

    /**
     * @var float
     */
    public $unit_price_ratio;

    /**
     * @var float
     */
    public $additional_shipping_cost;

    /**
     * @var boolean
     */
    public $customizable;

    /**
     * @var boolean
     */
    public $text_fields;

    /**
     * @var boolean
     */
    public $uploadable_files;

    /**
     * @var boolean
     */
    public $active;

    /**
     * @var integer
     */
    public $redirect_type;

    /**
     * @var integer
     */
    public $id_type_redirected;

    /**
     * @var boolean
     */
    public $available_for_order;

    /**
     * @var \DateTime
     */
    public $available_date;

    /**
     * @var boolean
     */
    public $show_condition;

    /**
     * @var string
     */
    public $condition;

    /**
     * @var boolean
     */
    public $show_price;

    /**
     * @var boolean
     */
    public $indexed;

    /**
     * @var string
     */
    public $visibility;

    /**
     * @var boolean
     */
    public $advanced_stock_management;

    /**
     * @var \DateTime
     */
    public $date_add;

    /**
     * @var \DateTime
     */
    public $date_upd;

    /**
     * @var integer
     */
    public $pack_stock_type;

    /**
     * @var string
     */
    public $meta_description;

    /**
     * @var string
     */
    public $meta_keywords;

    /**
     * @var string
     */
    public $meta_title;

    /**
     * @var string
     */
    public $link_rewrite;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $description_short;

    /**
     * @var string
     */
    public $available_now;

    /**
     * @var mixed
     */
    public $available_later;

    /**
     * @var float
     */
    public $total_price;

    /**
     * @var ProductDetailsAssociations
     */
    public $associations;

}