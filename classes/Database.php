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

    public static function register($driver, $dfactory, $gfactory)
    {
        static::$drivers[$driver] = array($dfactory, $gfactory);
    }

    public static function connection($name='')
    {
        // Normalize the name
        list($package, $element) = Package::split($name);
        if(strlen($element) == 0)
        {
            $element = Config::get(Package::join($package, 'database.default'));
            if($element === null)
            {
                throw new Exception('No default database');
            }
        }
        $name = Package::join($package, $element)

        // Connect if not already
        if(!isset(static::$cache[$name]))
        {
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
            list($dfactory, $gfactory) = static::$drivers[$driver];

            // Create driver
            if($dfactory instanceof \Closure)
            {
                $driver = $dfactory($settings);
            }
            else
            {
                $driver = new $dfactory($settings);
            }

            // Create grammar
            if($gfactory instanceof \Closure)
            {
                return $gfactory($driver, $settings);
            }
            else
            {
                return new $gfactory($driver, $settings);
            }

        }
        else
        {
            throw new Exception('Unknown database driver: ' . $driver);
        }
    }

}

