<?php

// File:        Database.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for database access

namespace MrBavii\SiteHelper;
use MrBavii\SiteHelper\Config;

class Database
{
    protected static $cache = array(); 
    protected static $drivers = array( 
    );

    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    public static function connection($name=null)
    {
        if($name === null)
        {
            $name = Config::get('database.default');
            if($name === null)
            {
                throw new Exception('No default database');
            }
        }

        if(!isset(static::$cache[$name]))
        {
            list($package, $element) = Package::split($name);

            $settings = Config::get(Package::join($package, 'database.connections.' . $element));
            if($settings === null)
            {
                throw new Exception('No database settings: ' . $name);
            }

            static::$cache[$name] = static::connect($settings);
        }

        return static::$cache[$name];
    }

    public static function connect($settings)
    {
        if(isset($settings['driver']))
        {
            $driver = $settings['driver'];
        }
        else
        {
            throw new Exception('No database driver');
        }

        if(isset(static::$drivers[$driver]))
        {
            $factory = static::$drivers[$driver];

            if($factory instanceof \Closure)
            {
                return $factory($settings);
            }
            else
            {
                return new $factory($settings);
            }
        }
        else
        {
            throw new Exception('Unknown database driver: ' . $driver);
        }
    }

}

