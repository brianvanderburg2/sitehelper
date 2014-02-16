<?php

namespace mrbavii\helper;

class Dispatcher
{
    public static function dispatch($path=null)
    {
        if($path === null)
        {
            $path = Request::getPathInfo();
        }

        // Split into parts
        $parts = explode('/', $path);
        if(strlen($parts[0] == 0)
        {
            array_shift($parts);
        }
    }

    public static function url($group, $entry, $params=array())
    {
    }

    protected static function encode($params)
    {
    }

    protected static function decode($path)
    {
    }
}

