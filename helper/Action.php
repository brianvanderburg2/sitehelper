<?php

namespace mrbavii\helper;

/**
 * A general action execution class
 *
 * \section helper_action_config Configuration
 *
 * - action.<group>.path\n
 *   The directory of the PHP files for the specific action group.
 * - action.<group>.<action>.config\n
 *   Configuration that is to be passed to a specific action as the $config parameter.
 */
class Action
{
    /**
     * Execute a specific action.
     *
     * The action will be sourced as a PHP script with parameters passed to it.
     *
     * \param $group The group of the action to execute.
     * \param $action The name of the action to execute.
     *  Each period in the action name is replaced with a directory separator.
     * \param $params An array of parameters to pass to the action.
     * \returns the return value of the sourced PHP script.
     */
    public static function execute($group, $action, $params=array())
    {
        // Determine the path to the action
        $path = Config::get("action.${group}.path");
        if($path === null)
        {
            throw new Exception("Unknown action group: ${group}");
        }
        else
        {
            // Notice: No security checks here.  It is assumed the action name is being
            // called directly, not user-supplied values
            $path .= '/' . str_replace('.', '/', $action) . '.php';

            // Determine the config for the action
            $params['config'] = Config::get("action.${group}.${action}.config");
            
            return Util::loadPhp($path, $params, TRUE);
        }
    }
}

