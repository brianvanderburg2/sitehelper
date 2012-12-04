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
        'sqlite3' => array('\MrBavii\SiteHelper\Database\Connectors\Sqlite3', '\MrBavii\SiteHelper\Database\Grammars\Sqlite')
    );

    public static function register($driver, $cfactory, $gfactory)
    {
        static::$drivers[$driver] = array($cfactory, $gfactory);
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
        $name = Config::join($group, $element);

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

