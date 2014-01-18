<?php

namespace mrbavii\helper;

class Action
{
    public static function execute($group, $action, $params=array())
    {
        // Determine the path to the action
        $path = Config::get('action.' . $group . '.path');
        if($path === null)
        {
            throw new Exception('Unknown action group: ' . $group);
        }
        else
        {
            // Notice: No security checks here.  It is assumed the action name is being
            // called directly, not user-supplied values
            $path .= '/' . str_replace('.', '/', $action) . '.php';

            // Determine the config for the action
            $params['config'] = Config::get('action.' . $group . '.' . $action);
            
            return Util::loadPhp($path, $params, TRUE);
        }
    }
}

