<?php

namespace mrbavii\helper;

class DispatcherEntry
{
    protected $pattern = null;
    protected $callback = null;
    protected $params = null;

    public function __construct($self, $pattern, $callback, $params)
    {
        $this->pattern = '/^' . str_replace('/', '\/', $pattern) . '\/?$/i';
        $this->callback = $callback;
        $this->params = $params;
    }

    public function execute($path)
    {
        // Do we match
        if(preg_match(static::$pattern, $path, $matches))
        {
            $cb = static::$callback;

            if($cb instanceof \Closure)
            {
                # Callback will have to check method directly
                $cb($matches, static::$params)
            }
            else
            {
                $obj = new $cb;
                $method = Request::getMethod();
                if(method_exists($obj, $method))
                {
                    $obj->$method($matches, $params);
                }
                else
                {
                    throw new Exception('Method not supported: ' . $method);
                }
            }

            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
        
}

class Dispatcher
{
    protected static $vars = array();
    protected static $entries = array();

    public static function register($pattern, $callback, $params)
    {
        static::$entries = new DispatcherEntry($pattern, $callback, $params);
    }

    public static function dispatch($path=null)
    {
        if($path === null)
        {
            $path = Request::getPathInfo();
        }

        $found = FALSE;
        foreach(static::$entries as $entry)
        {
            if($entry->execute($path))
            {
                $found = TRUE;
                break;
            }
        }

        return $found;
    }
}

