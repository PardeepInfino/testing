<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 11/01/2018
 * Time: 11:52
 */

namespace TMWK\ClientPrestashopApi;


class Config
{
    private static $url;
    private static $key;
    private static $debug;

    /**
     * @return mixed
     */
    public static function getUrl()
    {
        return self::$url;
    }

    /**
     * @param mixed $url
     */
    public static function setUrl($url)
    {
        self::$url = $url;
    }

    /**
     * @return mixed
     */
    public static function getKey()
    {
        return self::$key;
    }

    /**
     * @param mixed $key
     */
    public static function setKey($key)
    {
        self::$key = $key;
    }

    /**
     * @return mixed
     */
    public static function getDebug()
    {
        return self::$debug;
    }

    /**
     * @param mixed $debug
     */
    public static function setDebug($debug)
    {
        self::$debug = $debug;
    }


}