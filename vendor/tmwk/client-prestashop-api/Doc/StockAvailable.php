<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 06/10/2017
 * Time: 10:21
 */

namespace TMWK\ClientPrestashopApi\Doc;


class StockAvailable
{
    /**
     * @var StockAvailableDetails
     */
    public $stock_available;

    /**
     * @return OnlyChildren
     */
    public function children()
    {
        return new OnlyChildren();
    }
}