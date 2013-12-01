<?php

namespace mrbavii\helper;

class Action
{
    protected static $groups = array();

    public static function register($group, $path)
    {
        if(array_key_exists($group, static::$groups))
        {
            throw new Exception('Action group already registered: ' . $group);
        }
        static::$groups[$group] = $path;
    }

    public static function execute($group, $action, $params=array())
    {
        if(!array_key_exists($group, static::$groups))
        {
            throw new Exception('Unknown action group: ' . $group);
        }
        else
        {
            // Notice: No security checks here.  It is assumed the action name is being
            // called directly, not user-supplied values
            $path = static::$groups[$group] . '/' . str_replace('.', '/', $action) . '.php';
            
            return Util::loadPhp($path, $params, TRUE);
        }
    }
}

