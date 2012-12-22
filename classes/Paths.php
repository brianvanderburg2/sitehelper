<?php

// File:        Paths.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Manage paths for various resources

namespace MrBavii\SiteHelper;

class Paths
{
    protected static $paths = array();

    const FILTER_FILE=1;
    const FILTER_DIR=2;

    public static function register($name, $path, $url=null)
    {
        if(!isset(static::$paths[$name]))
        {
            static::$paths[$name] = array();
        }

        $path = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if($url !== null)
        {
            $url = rtrim($url, '/') . '/';
        }

        static::$paths[$name][] = array($path, $url);
    }

    protected static function check_filter($path, $filter)
    {
        switch($filter)
        {
            case static::FILTER_FILE:
                return is_file($path) && is_readable($path);

            case static::FILTER_DIR:
                return is_dir($path) && is_readable($path);

            case null:
                return TRUE;

            default:
                return FALSE;
        }

        return FALSE; // Should never get here
    }

    public static function find($name, $path, $filter=null)
    {
        if(!isset(static::$paths[$name]))
        {
            return array();
        }

        $path = ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $url = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        $results = array();
        foreach(static::$paths[$name] as $entry)
        {
            $full_path = $entry[0] . $path;
            if(static::check_filter($full_path, $filter))
            {
                $full_url = ($entry[1] === null) ? null : $entry[1] . $url;
                $results[] = array($full_path, $full_url);
            }
        }

        return $results;
    }

    public static function first($name, $path, $filter=null)
    {
        $results = static::find($name, $path, $filter);

        return count($results) ? $results[0] : null;
    }

    public static function last($name, $path, $filter=null)
    {
        $results = static::find($name, $path, $filter);

        return count($results) ? array_pop($results) : null;
    }
}

