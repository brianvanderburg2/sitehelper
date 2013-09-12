<?php

// File:        Database.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for database access

namespace mrbavii\sitehelper;
use mrbavii\sitehelper\Config;

class Database
{
    protected static $cache = array(); 
    protected static $drivers = array( 
        'sqlite3' => array('\mrbavii\sitehelper\database\connectors\Sqlite3', '\mrbavii\sitehelper\database\grammars\Sqlite')
    );

    public static function register($driver, $cfactory, $gfactory)
    {
        static::$drivers[$driver] = array($cfactory, $gfactory);
    }

    public static function connection($name=null)
    {
        // Get the name if needed
        if($name === null)
        {
            $name = Config::last('database.default');
            if($name === null)
            {
                throw new Exception('No default database');
            }
        }

        // Connect if not already
        if(!isset(static::$cache[$name]))
        {
            $settings = Config::last('database.connections.' . $name);
            if($settings === null)
            {
                throw new Exception('No database settings: ' . $name);
            }

            static::$cache[$name] = static::connect($settings);
        }

        return static::$cache[$name];
    }

    public static function connect($settings, $offset=0)
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
            list($cfactory, $gfactory) = static::$drivers[$driver];

            // Create connector
            if($cfactory instanceof \Closure)
            {
                $connector = $cfactory($settings, $gfactory);
            }
            else
            {
                $connector = new $cfactory($settings, $gfactory);
            }

            return $connector;

        }
        else
        {
            throw new Exception('Unknown database driver: ' . $driver);
        }
    }

}

