<?php

// File:        Config.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     A simple general purpose configuration class

namespace MrBavii\SiteHelper;

class Config
{
    protected static $packages = array();
    protected static $loaded_files = array();
    protected static $loaded_inis = array();
    protected static $loaded_configs = array();

    public static function set($name, $value)
    {
        $p = static::findParent($name);

        $p[0][$p[1]] = $value;
    }

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

    public static function merge($values, $where='')
    {
        $p = &static::findArray($where);
        foreach($values as $name => $value)
        {
            $p[$name] = $value;
        }
    }

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
        list($package, $name) = Package::split($name);
        $parts = explode('.', $name);
        $name = array_pop($parts);

        // Make sure the package config exists
        if(!isset(static::$packages[$package]))
        {
            if($make)
            {
                static::$packages[$package] = array();
            }
            else
            {
                return FALSE;
            }
        }

        // If the name part is empty, then parts will be empty and so will name
        // Will return the root array for the package and an empty name
        $target = &static::$packages[$package];
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

