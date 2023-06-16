<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 10:42
 */

namespace TMWK\ClientPrestashopApi\Doc;


class CombinationDetails
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
     * @var mixed
     */
    public $location;

    /**
     * @var string
     */
    public $ean13;

    /**
     * @var string
     */
    public $isbn;

    /**
     * @var string
     */
    public $upc;

    /**
     * @var integer
     */
    public $quantity;

    /**
     * @var string
     */
    public $reference;

    /**
     * @var string
     */
    public $supplier_reference;

    /**
     * @var float
     */
    public $wholesale_price;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $ecotax;

    /**
     * @var float
     */
    public $weight;

    /**
     * @var float
     */
    public $unit_price_impact;

    /**
     * @var integer
     */
    public $minimal_quantity;

    /**
     * @var integer
     */
    public $default_on;

    /**
     * @var mixed
     */
    public $available_date;

    /**
     * @var CombinationDetailsAssociations
     */
    public $associations;
}