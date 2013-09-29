<?php

// File:        Config.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     A simple general purpose configuration class

namespace mrbavii\sitehelper;

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
    public static function get($name)
    {
        $results = array();

        for($i = 0; $i < count(static::$data); $i++)
        {
            $p = static::findParent($name, $i);

            if($p !== FALSE && isset($p[0][$p[1]]))
            {
                $results[] = $p[0][$p[1]];
            }
        }

        return $results;
    }

    public static function first($name, $def=null)
    {
        $results = static::get($name);

        return (count($results) > 0) ? $results[0] : $def;
    }

    public static function last($name, $def=null)
    {
        $results = static::get($name);
        $count = count($results);

        return ($count > 0) ? $results[$count - 1] : $def;
    }

    /**
     * Add a group of configuration.
     *
     * @param config The configuration data for that group.
     */
    public static function add($config)
    {
        static::$data[] = $config;
    }

    protected static function findParent($name, $index)
    {
        $parts = explode('.', $name);
        $name = array_pop($parts);

        // Does the index exist
        if(!isset(static::$data[$index]))
        {
            return FALSE;
        }

        // If the name part is empty, then parts will be empty and so will name
        // Will return the root array for the group and an empty name
        $target = &static::$data[$index];
        foreach($parts as $part)
        {
            if(!isset($target[$part]) || !is_array($target[$part]))
            {
                return FALSE;
            }

            $target = &$target[$part];
        }
        
        return array(&$target, $name);
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

