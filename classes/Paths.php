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

    public static function add($name, $path)
    {
        if(!isset(static::$paths[$name]))
        {
            static::$paths[$name] = array();
        }

        static::$paths[$name][] = $path;
    }

    public static function find($name, $path, $filter=null)
    {
        // TODO: implement
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

