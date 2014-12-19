<?php

// File:        Database.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for PDO database access

namespace mrbavii\helper;

class Database
{
    protected static $cache = array(); 

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
        $instance = new database\Connection($settings);
        $instance->connect();
        return $instance;
    }
}

