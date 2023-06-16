<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 9:38
 */

namespace TMWK\ClientPrestashopApi\Doc;


class ProductDetailsAssociations
{
    /**
     * @var OnlyId[]
     */
    public $categories;

    /**
     * @var OnlyId[]
     */
    public $images;

    /**
     * @var ProductDetailsAssociationsProductFeatures[]
     */
    public $product_features;

    /**
     * @var ProductDetailsAssociationsStockAvailables[]
     */
    public $stock_availables;
}