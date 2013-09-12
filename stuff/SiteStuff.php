<?php

namespace mrbavii\sitestuff;
use \mrbavii\sitehelper as sh;

class SiteStuff
{
    public static function config($config=array())
    {
        // TODO: Load "app" config if any is needed
        sh\Config::group('user', $config);
    }

    public static function execute($pathinfo=null)
    {
        // Determine path info
        if($pathinfo === null)
        {
            if(isset($_SERVER['PATH_INFO']))
            {
                $pathinfo = $_SERVER['PATH_INFO'];
            }
            else
            {
                $pathinfo = '';
            }
        }

        // Break into parts and check
        $parts = explode('/', $pathinfo);
        array_shift($parts);

        foreach($parts as $part)
        {
            if(!preg_match("#^[a-zA-Z0-9][a-zA-Z0-9_\\.]*$#", $part))
            {
                die("Not Found"); // TODO: Use Event instead
            }
        }

        // Find the action and execute it
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'actions';
        
        while(count($parts) > 0)
        {
            $path = $path . DIRECTORY_SEPARATOR . array_shift($parts);
            $file = $path . '.php';

            if(file_exists($file))
            {
                sh\Util::loadPhp($file, array('params' => $parts), TRUE);
                exit();
            }
        }

        die("Not Found"); // TODO: Use Event instead
    }
}

