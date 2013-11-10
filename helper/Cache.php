<?php

// File:        Cache.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for cache access

namespace mrbavii\helper;

class Cache
{
    protected static $instance = null; 
    protected static $drivers = array(
        'null' => '\mrbavii\helper\cache\NullDriver',
        'memcache' => '\mrbavii\helper\cache\MemcacheDriver',
        'memory' => '\mrbavii\helper\cache\MemoryDriver',
        'redis' => '\mrbavii\helper\cache\RedisDriver',
        'apc' => '\mrbavii\helper\cache\ApcDriver',
        'file' => '\mrbavii\helper\cache\FileDriver',
        'database' => '\mrbavii\helper\cache\DatabaseDriver',
    );

    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    public static function instance()
    {
        if(isset(static::$instance))
        {
            return static::$instance;
        }

        // Get driver name and settings
        $driver = Config::get('cache.driver', 'memory');
        $settings = Config::get('cache.' . $driver, array());
        $settings['driver'] = $driver;

        return (static::$instance = static::connect($settings));
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

    // Quick access functions
    public static function set($name, $value, $lifetime=null)
    {
        return static::instance()->set($name, $value, $lifetime);
    }

    public static function get($name, $def=null)
    {
        return static::instance()->get($name, $def);
    }

    public static function remove($name)
    {
        return static::instance()->remove($name);
    }

    public static function connected()
    {
        return static::instance()->connnected();
    }

    public static function remember($name, $value, $lifetime=null)
    {
        return static::instance()->remember($name, $value, $lifetime);
    }
    
    public static function has($name)
    {
        return static::instance()->has($name);
    }

}
