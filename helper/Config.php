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
     * @param name The name of the configuration item to get.  This can be in
     * form 'name, or 'name.subname'.
     * @return All matching configuration items in an array, or an empty array if no matching items.
     */
    public static function get($name, $defval=null)
    {
        $parts = explode('.', $name);
        $name = array_pop($parts);
        
        $target = &static::$data;
        foreach($parts as $part)
        {
            if(array_key_exists($part, $target) && is_array($target[$part]))
            {
                $target = &$target[$part];
            }
            else
            {
                return $defval;
            }
        }

        if(array_key_exists($name, $target))
        {
            return $target[$name];
        }
        else
        {
            return $defval;
        }
    }

    /**
     * Set the configuration recursively.
     *
     * @param app_config The default application configuration.
     * @param user_config The user configuration.
     *
     * Any items in config may be named with a dot '.', and will automatically
     * be parsed into sub-arrays for configuration.
     *
     * Example:
     *
     * array(
     *     'database.connections' => array(
     *         'main' => array('driver' => 'sqlite3'),
     *         'main2.driver' => 'sqlite3'
     *     )
     * )
     *
     * Will be treated as:
     *
     * array(
     *     'database' => array(
     *         'connections' => array(
     *             'main' => array('driver' => 'sqlite3'),
     *             'main2' => array('driver' => sqlite3')
     *         )
     *     )
     * )
     *
     * If the matching items in both arrays are indexed arrays, then the
     * values from the user array will be appended to the values from the
     * application array
     *            
     */

    public function set($app_config, $user_config)
    {
        static::$data = array();
        static::set_helper(static::$data, $app_config);
        static::set_helper(static::$data, $user_config);
    }

    protected static function set_helper(&$target, &$source)
    {
        foreach($source as $key => $value)
        {
            if(is_int($key))
            {
                $target[] = $value;
            }
            else
            {
                $parts = explode('.', $key);
                $key = array_pop($parts);
                $tmptarget = &$target;

                foreach($parts as $part)
                {
                    if(array_key_exists($part, $tmptarget) && is_array($tmptarget[$part]))
                    {
                        $tmptarget = &$tmptarget[$part];
                    }
                    else
                    {
                        $tmptarget[$part] = array();
                        $tmptarget = &$tmptarget[$part];
                    }
                }

                if(is_array($value))
                {
                    if(array_key_exists($key, $tmptarget) && is_array($tmptarget[$key]))
                    {
                        static::set_helper($tmptarget[$key], $value);
                    }
                    else
                    {
                        $tmptarget[$key] = array();
                        static::set_helper($tmptarget[$key], $value);
                    }
                }
                else
                {
                    $tmptarget[$key] = $value;
                }
            }
        }
    }

}

