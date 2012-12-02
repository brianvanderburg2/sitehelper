<?php

// File:        Cache.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for cache access

namespace MrBavii\SiteHelper;

class Cache
{
    protected static $cache = array(); 
    protected static $drivers = array(
        'null' => '\MrBavii\SiteHelper\Cache\NullDriver',
        'memcache' => '\MrBavii\SiteHelper\Cache\MemcacheDriver',
        'memory' => '\MrBavii\SiteHelper\Cache\MemoryDriver',
        'redis' => '\MrBavii\SiteHelper\Cache\RedisDriver',
        'apc' => '\MrBavii\SiteHelper\Cache\ApcDriver',
        'file' => '\MrBavii\SiteHelper\Cache\FileDriver',
        'database' => '\MrBavii\SiteHelper\Cache\DatabaseDriver',
    );

    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    public static function driver($name='')
    {
        // Normalize the driver name
        list($group, $driver) = Config::split($name);
        if(strlen($driver) == 0)
        {
            $driver = Config::get(Config::join($group, 'cache.driver'), 'memory');
        }
        $name = Config::join($group, $driver);

        // Connect if not already
        if(!isset(static::$cache[$name]))
        {
            $settings = Config::get(Config::join($group, 'cache.' . $driver), array());
            $settings['driver'] = $driver;

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

