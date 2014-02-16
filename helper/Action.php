<?php

namespace mrbavii\helper;

/**
 * A general action execution class
 *
 * \section helper_action_config Configuration
 *
 * - action.<group>.<action>.callback\n
 *   The action callback used by call_user_func.  The callback parameters are the
 *   action configuration and any action parameters.
 * - action.<group>.<action>.config\n
 *   Configuration that is to be passed to a specific action as the $config parameter.
 */
class Action
{
    /**
     * Execute a specific action.
     *
     * \param $group The group of the action to execute.
     * \param $action The name of the action to execute.
     * \param $params An array of parameters to pass to the action.
     */
    public static function execute($group, $action, $params=array())
    {
        // Determine the action class, func
        $callback = Config::get("action.${group}.${action}.callback");
        if($callback === null)
        {
            throw new Exception("No action callback: ${group}.${action}");
        }
            
        // Determine the config for the action
        $config = Config::get("action.${group}.${action}.config", array());

        return call_user_func($callback, $config, $params);
    }
}

