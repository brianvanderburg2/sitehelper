<?php

namespace mrbavii\helper;

class Template
{
    protected static $cache = array();
    protected static $params = array();

    public static function get($group, $template, $params=null)
    {
        $path = static::find($group, $template);
        if($path === FALSE)
        {
            throw new Exception("No such template: ${group}.${template}");
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

    public static function find($group, $template)
    {
        if(isset(static::$cache["${group}.${template}"]))
        {
            $path = static::$cache["${group}.${template}"];
        }
        else
        {
            $path = FALSE;
            $paths = Config::get("template.${group}", array());
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

            static::$cache["${group}.${template}"] = $path;
        }

        return $path;
    }
}

