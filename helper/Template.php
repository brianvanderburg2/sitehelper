<?php

namespace mrbavii\helper;

class Template
{
    protected static $cache = array();
    protected static $params = array();

    public static function get($template, $params=null, $override=FALSE)
    {
        // Split into group/template parts
        $parts = explode('/', $template);
        if(count($parts) == 1)
        {
            $group = 'site';
            $template = $parts[0];
        }
        else if(count($parts) == 2)
        {
            $group = $parts[0];
            $template = $parts[1];
        }
        else
        {
            throw new Exception("No such template: ${template}");
        }
        
        // Find it
        $path = static::find($group, $template);
        if($path === FALSE)
        {
            throw new Exception("No such template: ${group}/${template}");
        }

        return static::getFile($path, $params, $override);
    }

    public static function getFile($path, $params=null, $override=FALSE)
    {
        $saved = null;
        if($params !== null)
        {
            $saved = static::$params;
            if($override)
            {
                static::$params = $params;
            }
            else
            {
                static::$params = array_merge(static::$params, $params);
            }
        }

        ob_start();
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

    public static function find($group, $template)
    {
        if(isset(static::$cache["${group}/${template}"]))
        {
            $path = static::$cache["${group}/${template}"];
        }
        else
        {
            $path = FALSE;
            $paths = Config::get("template.${group}.path", array());
            $name = '/' . str_replace('.', '/', $template) . '.php';

            foreach(array_reverse($paths) as $path)
            {
                $fullpath = $path . $name;
                if(file_exists($fullpath))
                {
                    $path = $fullpath;
                    break;
                }
            }

            static::$cache["${group}/${template}"] = $path;
        }

        return $path;
    }
}

