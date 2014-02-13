<?php

// File:        Database.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for database access

namespace mrbavii\helper;

class Database
{
    protected static $cache = array(); 
    protected static $drivers = array( 
        'sqlite2' => '\mrbavii\helper\database\connectors\Sqlite2',
        'sqlite3' => '\mrbavii\helper\database\connectors\Sqlite3'
    );

    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    public static function connection($name=null)
    {
        // Get the name if needed
        if($name === null)
        {
            $name = Config::get('database.default');
            if($name === null)
            {
                throw new Exception('No default database');
            }
        }

        // Connect if not already
        if(!isset(static::$cache[$name]))
        {
            $settings = Config::get('database.connections.' . $name);
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
            $factory = static::$drivers[$driver];

            // Create connector
            if($factory instanceof \Closure)
            {
                $instance = $factory($settings);
            }
            else
            {
                $instance = new $factory($settings);
            }

            $instance->connect();
            return $instance;

        }
        else
        {
            throw new Exception('Unknown database driver: ' . $driver);
        }
    }

}

