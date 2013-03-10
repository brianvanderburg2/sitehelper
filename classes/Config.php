<?php

// File:        Config.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     A simple general purpose configuration class

namespace mrbavii\sitehelper;

/**
 * Configuration storage class
 */
class Config
{
    protected static $splits = array();
    protected static $data = array();

    protected static $loaded_files = array();
    protected static $loaded_inis = array();
    protected static $loaded_configs = array();

    /**
     * Set a configuration item.
     *
     * @param name The name of the configuration item to set.  This can
     *  be 'name',  or 'name.subname'.  Setting a named item will replace
     *  all exising subnames.
     * @param value The value to set.
     */
    public static function set($name, $value)
    {
        $p = static::findParent($name);

        $p[0][$p[1]] = $value;
    }

    /**
     * Get a configuration item.
     *
     * @param name The name of the configuration item to get.  This can be in
     * form 'name, or 'name.subname'.
     * @param def The default return value if name is not found.
     * @return The value of the configuration item if self, else the default value.
     */
    public static function get($name, $def=null)
    {
        $p = static::findParent($name, FALSE);

        if($p !== FALSE && isset($p[0][$p[1]]))
        {
            return $p[0][$p[1]];
        }
        else
        {
            return $def;
        }
    }

    /**
     * Merge items into the array, keeping existing items.
     *
     * @param values The array of values to merge in.
     * @param where Where to merge the items in.  This can be in form
     *  'name' or 'name.subname'. If empty the root will be used.
     */
    public static function merge($values, $where='')
    {
        $p = &static::findArray($where);
        foreach($values as $name => $value)
        {
            $p[$name] = $value;
        }
    }

    /**
     * Merge items into the array, keeping existing items.  This
     * works similar to merge, except nested arrays are merged recursively.
     * If a value already set is an array but the value specified is not,
     * then the configuration will be overwritten.
     *
     * @param values The array of values to merge in.
     * @param where Where to merge the items in.
     */
    public static function mergeRecursive($values, $where='')
    {
        $p = &static::findArray($where);
        static::mergeHelper($p, $values);
    }

    protected static function mergeHelper(&$array1, &$array2)
    {
        foreach($array2 as $name => $value)
        {
            if(isset($array1[$name]) && is_array($array1[$name]) && is_array($array2[$name]))
            {
                static::mergeHelper($array1[$name], $array2[$name]);
            }
            else
            {
                $array1[$name] = $value;
            }
        }
    }

    protected static function findParent($name, $make=TRUE)
    {
        $parts = explode('.', $name);
        $name = array_pop($parts);

        // If the name part is empty, then parts will be empty and so will name
        // Will return the root array for the group and an empty name
        $target = &static::$data;
        foreach($parts as $part)
        {
            if(!isset($target[$part]) || !is_array($target[$part]))
            {
                if($make)
                {
                    $target[$part] = array();
                }
                else
                {
                    return FALSE;
                }
            }

            $target = &$target[$part];
        }
        
        return array(&$target, $name);
    }

    protected static function &findArray($name, $make=TRUE)
    {
        $p = static::findParent($name, $make);
        if(strlen($p[1]) > 0)
        {
            if(!isset($p[0][$p[1]]) || !is_array($p[0][$p[1]]))
            {
                if($make)
                {
                    $p[0][$p[1]] = array();
                }
                else
                {
                    return FALSE;
                }
            }

            return $p[0][$p[1]];
        }
        else
        {
            return $p[0];
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

