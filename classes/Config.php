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
    public static function get($name, $defval=null)
    {

        $p = static::findParent($name, FALSE);

        if($p !== FALSE && isset($p[0][$p[1]]))
        {
            return $p[0][$p[1]];
        }
        else
        {
            return $defval;
        }
    }

    /**
     * Set configuration.
     *
     * @param name_or_value The configuration item to set, or the entire config if value is not specified
     * @param value The value to set the configuration item to.
     */
    public static function set($name_or_value, $value=null)
    {
        if($value === null)
        {
            static::$data = $name_or_value;
        }
        else
        {
            $p = static::findParent($name_or_value, TRUE);
            $p[0][$p[1]] = $value;
        }
    }

    protected static function findParent($name, $create=FALSE)
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
                if($create)
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

