<?php

// File:        Route.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Route requests to specific functions based on the path

namespace MrBavii\SiteHelper;

class Route
{
    protected static $routes = array();
    protected static $named = array();
    protected static $group = array();

    public static function register($method, $pattern, $info)
    {
        $entry = new _RouteEntry($method, $pattern, $info, $settings);
        if($entry->name)
        {
            static::$named[$entry->name] = $entry;
        }
        
        $this->routes[] = $entry;
    }

    public static function get($pattern, $info)
    {
        return static::register('get', $pattern, $info);
    }

    public static function post($pattern, $info)
    {
        return static::register('post', $pattern, $info);
    }

    public static function any($pattern, $info)
    {
        return static::register('any', $pattern, $info);
    }

    public static function dispatch($path=null)
    {
        if($path === null)
        {
            $path == Request::path_info();
        }

        $handled = FALSE:
        foreach(static::$routes as $route)
        {
            if($route->handle($path))
            {
                $handled = TRUE;
                break;
            }
        }

        return $handled;
    }

    public static function group($settings, $callback)
    {
        $saved = static::$group;
        static::$group = array_merge(static::$group, $settings);

        call_user_func($callback);

        static::$group = $saved;
    }
}

class _RouteEntry
{
    protected $callback = null;
    protected $pattern = null;
    protected $https = FALSE;
    protected $name = null;
    protected $filters = null;

    public function __construct($pattern, $info, $settings)
    {
        // Merge info and settings, info takes priority
        if(!is_
        foreach($settings as $name => $value)
        {
            if
    }
}

