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

    /**
     * Determine the entry point used by the request.
     */
    public static function getEntryPoint()
    {
        return $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Dispatch the request by including another file.
     */
    public static function dispatch($dir, $params=array())
    {
        $path_info = static::getPathInfo();
        if(strlen($path_info) == 0)
            return FALSE;

        // Check each component in the path info
        $found = FALSE;
        $filename = $dir;

        $parts = explode('/', $path_info);
        if(count($parts) == 0)
            return FALSE;

        if(strlen($parts[0]) == 0) // First part is normally blank
            array_shift($parts);

        while(($part = array_shift($parts)) !== null)
        {
            // Add part to filename
            if(strlen($part) > 0 && Security::checkPathComponent($part))
            {
                $filename .= '/' . $part;
            }
            else
            {
                return FALSE;
            }

            // Check if file exists
            if(file_exists($filename . '.php'))
            {
                $found = TRUE;
                $filename = $filename . '.php';
                break;
            }
        }

        // Build remainder of parts
        if($found)
        {
            $params['pathinfo'] = implode('/', $parts);
            Util::loadPhp($filename, $params);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}

