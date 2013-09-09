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
    protected static $data = array();

    protected static $loaded_files = array();
    protected static $loaded_inis = array();
    protected static $loaded_configs = array();

    /**
     * Get a configuration item.
     *
     * @param name The name of the configuration item to get.  This can be in
     * form 'name, or 'name.subname'.
     * @param def The default return value if name is not found.
     * @param order The order that the groups are searched.
     * @return The value of the configuration item if self, else the default value.
     */
    public static function get($name, $def=null, $order="user,application")
    {
        $group_names = explode(',', $order);
        foreach($group_names as $group_name)
        {
            $p = static::findParent($name, $group_name);

            if($p !== FALSE && isset($p[0][$p[1]]))
            {
                return $p[0][$p[1]];
            }
        }

        return $def;
    }

    /**
     * Set a configuration group.
     *
     * @param name The name of the group to set
     * @param config THe configuration data for that group.
     */
    public static function group($name, $config)
    {
        static::$data[$name] = $config;
    }

    protected static function findParent($name, $group)
    {
        $parts = explode('.', $name);
        $name = array_pop($parts);

        // Does the group exist
        if(!isset(static::$data[$group]))
        {
            return FALSE;
        }

        // If the name part is empty, then parts will be empty and so will name
        // Will return the root array for the group and an empty name
        $target = &static::$data[$group];
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

