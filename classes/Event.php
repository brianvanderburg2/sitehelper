<?php

// File:        Event.php
// Author:      Brian Allen Vanderburg II
// Purpose:     A simple event dispatcher/hook class

namespace MrBavii\SiteHelper;

class Event
{
    protected static $events = array();

    public static function listen($event, $callback)
    {
        if(!isset(static::$events[$event]))
        {
            static::$events[$event] = array();
        }

        static::$events[$event][] = $callback;
    }

    public static function fire($event)
    {
        if(!isset(static::$events[$event]))
        {
            return;
        }

        $args = func_get_args();
        array_shift($args);

        foreach(static::$events[$event] as $callback)
        {
            call_user_func_array($callback, $args);
        }
    }
}

