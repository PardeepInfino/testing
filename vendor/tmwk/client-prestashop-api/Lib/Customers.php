<?php
/**
 * Created by PhpStorm.
 * User: mfigueroa
 * Date: 07/10/2017
 * Time: 2:57
 */

namespace TMWK\ClientPrestashopApi\Lib;

use TMWK\ClientPrestashopApi\Entities\Customers as Entity;
use TMWK\ClientPrestashopApi\Doc\Customers as Cust;
use TMWK\ClientPrestashopApi\Doc\Customer;

class Customers extends WebService
{
    private static $_resource = 'customers';

    /**
     * @param $parameters array
     * @return Cust
     */
    public function findAll(array $parameters = array())
    {
        return $this->wsFindAll(self::$_resource, $parameters);
    }

    /**
     * @param integer $id
     * @param array $parameters
     * @return Customer
     */
    public function find($id, array $parameters = array())
    {
        return$this->wsFind(self::$_resource, $id, $parameters);
    }


    /**
     * @param Entity $data
     * @return Customer
     */
    public function create(Entity $data)
    {
        return $this->wsCreate(self::$_resource, $data->getParameters());
    }

    /**
     * @param Entity $data
     * @return Customer
     */
    public function update(Entity $data)
    {
        $id = $data->getId();
        return $this->wsUpdate(self::$_resource, $id, $data->getParameters());
    }

    /**
     * @param $id
     * @return bool|\SimpleXMLElement|string
     */
    public function delete($id)
    {
        return $this->wsDelete(self::$_resource, $id);
    }
}