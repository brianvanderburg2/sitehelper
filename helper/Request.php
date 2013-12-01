<?php

namespace mrbavii\helper;

/**
 * The class for handling requests.
 */
class Request extends Browser
{
    /**
     * Determine the request method
    */
    public static function getMethod()
    {
        static $method = null;
        if($method === null)
        {
            switch($_SERVER['REQUEST_METHOD'])
            {
                case 'GET':
                    $method = 'get';
                    break;
                case 'HEAD':
                    $method = 'head';
                    break;
                case 'POST':
                    $method = 'post';
                    break;
                default:
                    $method = 'unknown';
                    break;
            }
        }

        return $method;
    }

    /**
     * Determine the PATH_INFO
     */
    public static function getPathInfo($calc=FALSE)
    {
        if($calc || !array_key_exists('PATH_INFO', $_SERVER))
        {
            $pathinfo = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
            if(($pos = strpos($pathinfo, '?' . $_SERVER['QUERY_STRING'])) !== FALSE)
            {
                $pathinfo = substr($pathinfo, 0, $pos);
            }

            return $pathinfo;   
        }
        else
        {
            return $_SERVER['PATH_INFO'];
        }
    }
}

