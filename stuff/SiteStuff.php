<?php

namespace mrbavii\sitestuff;
use \mrbavii\sitehelper as sh;

/**
 * Main entry point for the "stuff"
 */
class SiteStuff
{
    public static function config($user=array())
    {
        // Add user config first since they are used in order that they are added
        require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

        sh\Config::add($user);
        sh\Config::add($config);

        // Register event listeners
        sh\Event::listen('404', function(){ Action::
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
                $pathinfo = '/index';
            }
        }

        Action::execute(substr($pathinfo, 1));
    }
}

