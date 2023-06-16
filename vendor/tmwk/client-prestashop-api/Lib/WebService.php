<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 11/01/2018
 * Time: 16:35
 */

namespace TMWK\ClientPrestashopApi\Lib;

use TMWK\ClientPrestashopApi\Config;
use TMWK\ClientPrestashopApi\PSWebservice;
use SimpleXMLElement;

class WebService
{
    /**
     * @param $service
     * @param array $parameters
     * @return SimpleXMLElement|string
     */
    protected function wsFindAll($service, array $parameters)
    {
        if (!array_key_exists('display', $parameters)) {
            $parameters['display'] = 'full';
        }
        return $this->get($service, $parameters);
    }

    /**
     * @param $service
     * @param array $parameters
     * @return SimpleXMLElement|string
     */
    protected function wsFindIndex($service, array $parameters)
    {
        if (array_key_exists('display', $parameters)) {
            unset($parameters['display']);
        }
        return $this->get($service, $parameters);
    }

    /**
     * @param $service
     * @param $id
     * @param array $parameters
     * @return SimpleXMLElement|string
     */
    protected function wsFind($service, $id, array $parameters)
    {
        if (array_key_exists('display', $parameters)) {
            unset($parameters['display']);
        }
        return $this->get($service . '/' . $id, $parameters);
    }

    /**
     * @param $service
     * @param array $fields
     * @return SimpleXMLElement|string
     */
    protected function wsCreate($service, array $fields)
    {
        /**
         * @var $xml \SimpleXMLElement
         */
        $webService = new PSWebservice(Config::getUrl(), Config::getKey(), Config::getDebug());

        try {
            $xml = $webService->get(array('url' => Config::getUrl() . 'api/' . $service . '?schema=blank&ws_key=' . Config::getKey()));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $resources = $xml->children()->children();

        $this->processData($resources, $fields);

        $opt = array(
            'resource' => $service,
            'ws_key'   => Config::getKey(),
            'postXml'  => $xml->asXML()
        );

        try {
            $xml = $webService->add($opt);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $xml;
    }

    /**
     * @param $service
     * @param $id
     * @param array $fields
     * @return SimpleXMLElement|string
     */
    protected function wsUpdate($service, $id, array $fields)
    {
        /**
         * @var $xml \SimpleXMLElement
         */
        $webService = new PSWebservice(Config::getUrl(), Config::getKey(), Config::getDebug());

        try {
            $xml = $webService->get(array('url' => Config::getUrl() . 'api/' . $service . '?schema=blank&ws_key=' . Config::getKey()));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $resources = $xml->children()->children();

        $this->processData($resources, $fields);

        $opt = array(
            'resource' => $service,
            'id'       => $id,
            'ws_key'   => Config::getKey(),
            'putXml'   => $xml->asXML()
        );

        try {
            $xml = $webService->edit($opt);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $xml;
    }

    /**
     * @param SimpleXMLElement $resources
     * @param $fields
     * @return bool
     */
    public function processData(SimpleXMLElement &$resources, $fields)
    {
        foreach ($resources as $k => $v) {
            if (isset($fields[$k]) && is_array($fields[$k])) {
                return $this->processData($v, $fields[$k]);
            } else {
                if (isset($fields[$k])) {
                    $resources->$k = $fields[$k];
                } else {
                    foreach ($fields as $key => $field) {
                        foreach ($field as $k1 => $v1) {
                            foreach ($v1 as $k2 => $v2) {
                                $resources->addChild($k1)->addChild($k2, $v2);
                            }

                        }
                    }
                    return true;
                }
            }
        }
        return true;
    }

    /**
     * @param $service
     * @param $id
     * @return bool|SimpleXMLElement|string
     */
    protected function wsDelete($service, $id)
    {
        /**
         * @var $xml \SimpleXMLElement
         */
        $webService = new PSWebservice(Config::getUrl(), Config::getKey(), Config::getDebug());

        $opt = array(
            'resource' => $service,
            'id'       => $id
        );

        try {
            $xml = $webService->delete($opt);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $xml;
    }

    public function getBlank()
    {
        /**
         * @var $xml \SimpleXMLElement
         */
        $webService = new PSWebservice(Config::getUrl(), Config::getKey(), Config::getDebug());

        try {
            return $webService->get(array('url' => Config::getUrl() . 'api/' . 'specific_prices' . '?output_format=XML&schema=blank&ws_key=' . Config::getKey()));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $service
     * @param $parameters
     * @return \SimpleXMLElement|SimpleXMLElement|string
     */
    protected function get($service, $parameters)
    {
        /**
         * @var $resources \SimpleXMLElement
         */
        $webService = new PSWebservice(Config::getUrl(), Config::getKey(), Config::getDebug());

        $params             = array();
        $params['resource'] = $service;
        if (!array_key_exists('output_format', $parameters)) {
            $params['output_format'] = 'JSON';
        }

        foreach ($parameters as $key => $parameter) {
            $params[$key] = $parameter;
        }

        try {
            $resources = $webService->get($params);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $resources;
    }
}