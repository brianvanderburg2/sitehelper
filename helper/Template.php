<?php

namespace mrbavii\helper;

class Template
{
    protected static $cache = array();
    protected static $params = array();

    public static function get($group, $template, $params=null)
    {
        // Determine the path to the template
        if(isset(static::$cache["${group}.${template}"]))
        {
            $path = static::$cache["${group}.${template}"];
        }
        else
        {
            $path = Config::get("template.${group}.${template}.path");
            if($path === null)
            {
                $path = Config::get("template.${group}.path");
                if($path === null)
                {
                    throw new Exception("Unknown template group: ${group}");
                }
                else
                {
                    $path = $path . '/' . str_replace('.', '/', $template) . '.php';
                }
            }

            static::$cache["${group}.${template}"] = $path;
        }

        ob_start();
        $saved = null;
        if($params !== null)
        {
            $saved = static::$params;
            static::$params = array_merge(static::$params, $params);
        }

        try
        {
            Util::loadPhp($path, static::$params, TRUE);

            if($saved !== null)
            {
                static::$params = $saved;
            }

            return ob_get_clean();
        }
        catch(\Exception $e)
        {
            if($saved != null)
            {
                static::$params = $saved;
            }
            ob_end_clean();

            throw $e;
        } 
    }
}

