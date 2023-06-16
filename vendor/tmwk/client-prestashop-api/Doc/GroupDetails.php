<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 17/01/2018
 * Time: 10:50
 */

namespace TMWK\ClientPrestashopApi\Doc;


class GroupDetails
{
    public $id;

    public $reduction;

    public $price_display_method;

    public $show_prices;

    public $date_add;

    public $date_upd;

    /**
     * @var GroupDetailsLanguage
     */
    public $name;
}