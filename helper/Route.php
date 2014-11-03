<?php

namespace mrbavii\helper;


class Route
{
    protected static $routes = array();
    protected static $named = array();

    public static function register($pattern, $callback, $name=null)
    {
        static::$routes[] = array(static::patternToRegex($pattern), $callback, $name);
        if($name !== null)
        {
            static::$named[$name] = $pattern;
        }
    }

    public static function dispatch($route=null)
    {
        // Determine the route
        if($route === null)
        {
            $route = Request::getPathInfo();
        }

        // Find a match
        foreach(static::$routes as $entry)
        {
            list($pattern, $callback, $name) = $entry;

            $params = array();
            if(preg_match($pattern, $route, $params))
            {
                $config = array();
                if($name !== null)
                {
                    $config = Config::get('route.' . $name . '.config', array());
                }

                call_user_func($callback, $params, $config);
                return TRUE;
            }
        }

        // No match found
        Event::fire('error.404');
        return FALSE;
    }

    public static function url($named, $params)
    {
        if(isset(static::$named[$named]))
        {
            $route = preg_replace_callback(
                '#\\{.*?\\}#',
                function($matches) use($params) { return $params[$matches[0]]; }, # TODO: url encode the match if needed
                static::$named[$named]
            );

            # TODO: combine with scheme, script name, proper url encoding if needed
            # Individual matches may need to be URL encoded if they may contain special characters,
            # while the rest of the match string should not (it it needs it, it should be escaped properly in register)
        }

        return FALSE;
    }

    protected static function pieceCallback($matches)
    {
    }

    protected static function paramCallback($matches)
    {
    }

    protected static function patternToRegex($pattern)
    {
        // Build the regex from the pattern
        if($pattern == '*')
        {
            return '#.*#';
        }

        $parts = preg_split('#\\{.*?\\}#', $pattern, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $regex = "";

        foreach($parts as $part)
        {
            if($part[0] == '{')
            {
                # TODO: build regex for parameters
            }
            else
            {
                $regex .= preg_quote($part);
            }
        }

        return '#^' . $regex . '$#';
    }
}

