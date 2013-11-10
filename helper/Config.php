<?php

// File:        Config.php
// Author:      Brian Allen Vanderburg Ii
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
     * Set configuration.
     *
     * @param config The configuration to set.
     */
    public static function set($config)
    {
        static::$data = array();
        static::merge($config);
    }

    /**
     * Merge in configuration recursively.
     *
     * @param config The configuration to merge.
     */
    public static function merge($config)
    {
        static::merge_helper(static::$data, $config);
    }

    protected static function merge_helper(&$target, &$source)
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

                foreach($parts as $part)
                {
                    if(array_key_exists($part, $target) && is_array($target[$part]))
                    {
                        $target = &$target[$part];
                    }
                    else
                    {
                        $target[$part] = array();
                        $target = &$target[$part];
                    }
                }

                if(is_array($value) && array_key_exists($key, $target) && is_array($target[$key]))
                {
                    static::merge_helper($target[$key], $value);
                }
                else
                {
                    $target[$key] = $value;
                }
            }
        }
    }

    /**
     * @todo: document
     */
    public static function parse($string, $sep=',', $eq='=')
    {
        $results = array();

        $parts = explode($sep, $string);
        foreach($parts as $part)
        {
            $pos = strrpos($part, $eq);
            if($pos !== FALSE)
            {
                $name = substr($part, 0, $pos);
                $value = substr($part, $pos + 1);
            }
            else
            {
                $name = $part;
                $value = TRUE;
            }

            if(isset($results[$name]))
            {
                if(!is_array($results[$name]))
                {
                    $results[$name] = array($results[$name]);
                }

                $results[$name][] = $value;
            }
            else
            {
                $results[$name] = $value;
            }
        }

        return $results;
    }
}
