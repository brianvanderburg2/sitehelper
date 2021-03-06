<?php

// File:        Cache.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Configuration and connections for cache access

namespace mrbavii\helper;

/**
 * \defgroup helper_cache_drivers Cache Drivers
 */

/**
 * A basic caching class
 *
 * \section helper_cache_config Configuration
 *
 * - cache.driver\n
 *   The name of the driver to use for the default cache instance.
 * - cache.<driver>\n
 *   Configuration for the specific cache driver.
 *
 * \sa helper_cache_drivers
 */
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

    /**
     * Register a custom cache driver
     *
     * \param $driver The name of the driver.
     * \param $factory The class name or callback function used to create the cache driver.
     */
    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    /**
     * Return an instance of the cache driver or set the active instance.
     *
     * \param $instance An instance to set, or FALSE to get an instance.
     * \return An instance of the configured cache driver, or the previous instance
     *  when setting an instance.
     */
    public static function instance($instance=FALSE)
    {
        if($instance !== FALSE)
        {
            $tmp = static::$instance;
            static::$instance = $instance;
            return $tmp;
        }

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

