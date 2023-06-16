<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 10:22
 */

namespace TMWK\ClientPrestashopApi\Doc;


class StockAvailableDetails
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var integer
     */
    public $id_product;
    /**
     * @var integer
     */
    public $id_product_attribute;
    /**
     * @var integer
     */
    public $id_shop;
    /**
     * @var integer
     */
    public $id_shop_group;
    /**
     * @var integer
     */
    public $quantity;
    /**
     * @var boolean
     */
    public $depends_on_stock;
    /**
     * @var integer
     */
    public $out_of_stock;
}