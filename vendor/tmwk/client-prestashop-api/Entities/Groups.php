<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 17/01/2018
 * Time: 8:34
 */

namespace TMWK\ClientPrestashopApi\Entities;


use TMWK\ClientPrestashopApi\Config;
use TMWK\ClientPrestashopApi\PrestaShopWebService;

class Groups
{
    private $id = null;

    private $reduction = 0;

    private $price_display_method = 0;

    private $show_prices = 0;

    private $date_add;

    private $date_upd;

    private $name;

    public function __construct($id = null)
    {
        if ($id !== null) {
            $es = new PrestaShopWebService(Config::getUrl(), Config::getKey(), Config::getDebug());

            $entity                     = $es->Groups()->find($id);
            $this->id                   = $entity->group->id;
            $this->reduction            = $entity->group->reduction;
            $this->price_display_method = $entity->group->price_display_method;
            $this->show_prices          = $entity->group->show_prices;
            $this->date_add             = $entity->group->date_add;
            $this->date_upd             = $entity->group->date_upd;
            $this->name                 = $entity->group->name;

        } else {
            $date           = new \DateTime();
            $this->date_add = $date->format('Y-m-d H:i:s');
            $this->date_upd = $date->format('Y-m-d H:i:s');
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     * @return Groups
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $reduction
     * @return Groups
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;
        return $this;
    }

    /**
     * @param boolean $price_display_method
     * @return Groups
     */
    public function setPriceDisplayMethod($price_display_method)
    {
        $this->price_display_method = $price_display_method;
        return $this;
    }

    /**
     * @param boolean $show_prices
     * @return Groups
     */
    public function setShowPrices($show_prices)
    {
        $this->show_prices = $show_prices;
        return $this;
    }

    /**
     * @param string $date_add
     * @return Groups
     */
    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
        return $this;
    }

    /**
     * @param string $date_upd
     * @return Groups
     */
    public function setDateUpd($date_upd)
    {
        $this->date_upd = $date_upd;
        return $this;
    }

    /**
     * @param string $name
     * @return Groups
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return array(
            'id'                   => $this->getId(),
            'reduction'            => $this->reduction,
            'price_display_method' => $this->price_display_method,
            'show_prices'          => $this->show_prices,
            'date_add'             => $this->date_add,
            'date_upd'             => $this->date_upd,
            'name'                 => array(
                'language' => $this->name
            ),
        );
    }
}