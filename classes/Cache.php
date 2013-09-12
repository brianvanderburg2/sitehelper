<?php

// File:        Cache.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for cache access

namespace mrbavii\sitehelper;

class Cache
{
    protected static $cache = array(); 
    protected static $drivers = array(
        'null' => '\mrbavii\sitehelper\cache\NullDriver',
        'memcache' => '\mrbavii\sitehelper\cache\MemcacheDriver',
        'memory' => '\mrbavii\sitehelper\cache\MemoryDriver',
        'redis' => '\mrbavii\sitehelper\cache\RedisDriver',
        'apc' => '\mrbavii\sitehelper\cache\ApcDriver',
        'file' => '\mrbavii\sitehelper\cache\FileDriver',
        'database' => '\mrbavii\sitehelper\cache\DatabaseDriver',
    );

    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    public static function driver($driver=null)
    {
        // Get the driver if needed
        if($driver === null)
        {
            $driver = Config::last('cache.driver', 'memory');
        }

        // Connect if not already
        if(!isset(static::$cache[$driver]))
        {
            $settings = Config::last('cache.' . $driver, array());
            $settings['driver'] = $driver;

            static::$cache[$driver] = static::connect($settings);
        }

        return static::$cache[$driver];
    }

    public static function connect($settings)
    {
        if(isset($settings['driver']))
        {
            $driver = $settings['driver'];
        }
        else
        {
            throw new Exception('No cache driver');
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
            throw new Exception('Unknown cache driver: ' . $driver);
        }
    }
}

