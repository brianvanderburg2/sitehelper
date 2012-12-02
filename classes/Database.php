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

    public static function register($driver, $dfactory, $cfactory)
    {
        static::$drivers[$driver] = array($dfactory, $cfactory);
    }

    public static function connection($name='')
    {
        // Normalize the name
        list($group, $element) = Config::split($name);
        if(strlen($element) == 0)
        {
            $element = Config::get(Config::join($group, 'database.default'));
            if($element === null)
            {
                throw new Exception('No default database');
            }
        }
        $name = Config::join($group, $element)

        // Connect if not already
        if(!isset(static::$cache[$name]))
        {
            $settings = Config::get(Config::join($group, 'database.connections.' . $element));
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
            list($dfactory, $cfactory) = static::$drivers[$driver];

            // Create driver
            if($dfactory instanceof \Closure)
            {
                $driver = $dfactory($settings);
            }
            else
            {
                $driver = new $dfactory($settings);
            }

            // Create connection
            if($cfactory instanceof \Closure)
            {
                return $cfactory($driver, $settings);
            }
            else
            {
                return new $cfactory($driver, $settings);
            }

        }
        else
        {
            throw new Exception('Unknown database driver: ' . $driver);
        }
    }

}

