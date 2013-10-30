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
}

