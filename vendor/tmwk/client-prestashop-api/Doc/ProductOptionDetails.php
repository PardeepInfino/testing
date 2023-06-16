<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 16:24
 */

namespace TMWK\ClientPrestashopApi\Doc;


class ProductOptionDetails
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var
     */
    public $is_color_group;

    /**
     * @var
     */
    public $group_type;

    /**
     * @var
     */
    public $position;

    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $public_name;

    /**
     * @var ProductOptionDetailsProductOptionValues
     */
    public $associations;
}