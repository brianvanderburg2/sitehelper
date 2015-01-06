<?php

// File:        Config.php
// Author:      Brian Allen Vanderburg II
// Purpose:     A simple general purpose configuration class

namespace mrbavii\helper;

/**
 * Configuration storage class.
 */
class Config
{
    protected static $data = array();

    /**
     * Get a configuration item.
     *
     * @param name The name of the configuration item to get.
     * @param defval The default value to return if not found.
     * @return The last added configuration item of the specified name.
     */
    public static function get($name, $defval=null)
    {
        if(isset(static::$data[$name]))
        {
            return end(static::$data[$name]);
        }
        else
        {
            return $defval;
        }
    }

    /**
     * Get all the configuration items.
     *
     * @param name The name of the configuration item to get.
     * @return All the added configuration items, or an empty array().
     *
     * The returned array will be reversed, so the last added item will be first.
     */
    public static function all($name)
    {
        if(isset(static::$data[$name]))
        {
            return array_reverse(static::$data[$name]);
        }
        else
        {
            return array();
        }
    }

    /**
     * Add data to the configuration.
     *
     * @param $config The configuration to add.
     *
     * Each key gets it's own array, and values are added to the end of the
     * array. The general idea is that app/default configuration is added first,
     * then user/overriden configuration is added next.
     */
    public static function add($config)
    {
        foreach($config as $key => $value)
        {
            if(!isset(static::$data[$key]))
            {
                static::$data[$key] = array($value);
            }
            else
            {
                static::$data[$key][] = $value;
            }
        }
    }

    /**
     * Clear the configuration
     */
    public static function clear()
    {
        static::$data = array();
    }
}

