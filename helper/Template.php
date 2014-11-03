<?php

namespace mrbavii\helper;

class _TemplateCaller
{
    public function send($template, $params=null, $override=FALSE)
    {
        Template::send($template, $params, $override);
    }

    public function get($template, $params=null, $override=FALSE)
    {
        return Template::get($template, $params, $override);
    }

    public function __call($name, $args)
    {
        return Template::callFunction($name, $args);
    }
}

class Template
{
    protected static $fn = array();
    protected static $cache = array();
    protected static $params = array();
    protected static $paths = array();
    protected static $caller = null;
 
    public static function registerFunction($name, $callback)
    {
        static::$fn[$name] = $callback;
    }

    public static function callFunction($name, $args)
    {
        if(isset(static::$fn[$name]))
        {
            return call_user_func_array(static::$fn[$name], $args);
        }
        else
        {
            throw new Exception("No such template function: ${name}");
        }
    }

    public static function addSearchPath($path, $prefix=null)
    {
        if($prefix !== null)
        {
            $prefix = trim($prefix, '.') . '.';
        }

        static::$paths[] = array($path, $prefix);
    }

    public static function send($template, $params=null, $override=FALSE)
    {
        print static::get($template, $params, $override);
    }

    public static function get($template, $params=null, $override=FALSE)
    {
        // Find it
        $path = static::find($template);
        if($path === FALSE)
        {
            throw new Exception("No such template: ${template}");
        }

        return static::getFile($path, $params, $override);
    }

    public static function getFile($path, $params=null, $override=FALSE)
    {
        if(static::$caller === null)
            static::$caller = new _TemplateCaller();

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

        // Always set self
        static::$params['self'] = static::$caller;

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

    public static function find($template)
    {
        // Check cache first
        if(isset(static::$cache[$template]))
        {
            return static::$cache[$template];
        }

        // Find it
        foreach(array_reverse(static::$paths) as $entry)
        {
            list($path, $prefix) = $entry;

            if($prefix === null)
            {
                $the_template = $template;
            }
            else
            {
                if(Util::startsWith($template, $prefix))
                {
                    $the_template = substr($template, strlen($prefix));
                }
                else
                {
                    continue;
                }
            }

            $the_path = $path . '/' . str_replace('.', '/', $the_template) . '.php';
            if(file_exists($the_path))
            {
                static::$cache[$template] = $the_path;
                return $the_path;
            }
        }

        // Could not find it
        return FALSE;
    }
}

