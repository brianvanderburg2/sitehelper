<?php

namespace mrbavii\helper;


class Route
{
    protected static $routes = array();
    protected static $named = array();

    protected static $regex = array(
        'num' => '[0-9]+',
        'alpha' => '[a-zA-Z]+',
        'alnum' => '[a-zA-Z0-9]+',
        'ident' => '[a-zA-Z0-9_]+',
        'any' => '[^/]+',
        'all' => '.*'
    );

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

                // We do not urldecode the params, even :any, in case the route
                // callback wishes to tell the difference between say '/' and '%2F'
                call_user_func($callback, $params, $config);
                return TRUE;
            }
        }

        // No match found
        Event::fire('error.404');
        return FALSE;
    }

    public static function url($named, $params=array())
    {
        if(isset(static::$named[$named]))
        {
            $route = preg_replace_callback(
                '#\\{(.*?)(:.*?)?\\}#',
                function($matches) use($params) { return urlencode(strval($params[$matches[1]])); },
                static::$named[$named]
            );

            $route = $_SERVER['SCRIPT_NAME'] . $route;
            return $route;
        }

        return FALSE;
    }

    protected static function patternToRegex($pattern)
    {
        // Build the regex from the pattern
        if($pattern == '*')
        {
            return '#.*#';
        }

        $parts = preg_split('#(\\{.*?\\})#', $pattern, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $regex = "";

        foreach($parts as $part)
        {
            if($part[0] == '{' && substr($part, -1) == '}')
            {
                # Remove '{', '}'
                $part = substr($part, 1, -1);
                if($part === FALSE || strlen($part) == 0)
                    continue;

                # Split on ':'
                $part = explode(':', $part);
                if(count($part) > 1)
                {
                    $name = $part[0];
                    $type = $part[1];
                }
                else
                {
                    $name = $part[0];
                    $type = 'any';
                }

                # Append named regular expression part
                if(!isset(static::$regex[$type]))
                {
                    # TODO: should probably raise an exception instead
                    $type = 'any';
                }

                $type = static::$regex[$type];
                $regex .= "(?P<{$name}>{$type})";
            }
            else
            {
                $regex .= preg_quote($part);
            }
        }

        return '#^' . $regex . '$#';
    }
}

