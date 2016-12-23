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
    protected static $paths = null;
    protected static $caller = null;
    protected static $current = "";
 
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

    public static function send($template, $params=null, $override=FALSE)
    {
        print static::get($template, $params, $override);
    }

    public static function get($template, $params=null, $override=FALSE)
    {
        // Find it
        $original = static::$current;

        $norm = static::normalize($template);
        if($norm === FALSE)
        {
            throw new Execption("Unable to normalize template: ${template} from: ${original}");
        }

        $path = static::find($norm);
        if($path === FALSE)
        {
            throw new Exception("No such template: ${template} from: ${original}");
        }

        try
        {
            static::$current = $norm;
            $result = static::getFile($path, $params, $override);
            static::$current = $original;

            return $result;
        }
        catch(\Exception $e)
        {
            static::$current = $original;
            throw $e;
        }
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
        if(static::$paths === null)
        {
            static::$paths = Config::all('template.path');
        }

        foreach(static::$paths as $part)
        {
            $part = (array)$part;

            foreach($part as $entry)
            {
                if(is_array($entry))
                {
                    list($prefix, $path) = $entry;
                }
                else
                {
                    $prefix = null;
                    $path = $entry;
                }

                if($prefix === null)
                {
                    $the_template = $template;
                }
                else if(Util::startsWith($template, $prefix))
                {
                    $the_template = substr($template, strlen($prefix));
                }
                else
                {
                    continue;
                }

                $the_path = $path . '/' . $the_template . '.php';
                if(file_exists($the_path))
                {
                    static::$cache[$template] = $the_path;
                    return $the_path;
                }
            }
        }
 
        // Could not find it
        static::$cache[$template] = FALSE;
        return FALSE;
    }

    protected static function normalize($template)
    {
        $parts = explode("/", $template);

        if($parts[0] == "")
        {
            # It is absolute
            $result = array();
            array_shift($parts);
        }
        else if(strlen(static::$current) == 0)
        {
            # Treated as relative to the root template namespace
            $result = array();
        }
        else
        {
            # Relative to the current template location
            $result = explode("/", static::$current);

            # Because templates are files, the path is really one less
            # Template admin/view is file view under directory admin, so
            # a relative template "header" should be admin/header not
            # admin/view/header
            array_pop($result);
        }

        # Handle '.' and '..'
        foreach($parts as $part)
        {
            if(strlen($part) == 0)
            {
                return FALSE;
            }
            else if($part == ".")
            {
                continue;
            }
            else if($part == "..")
            {
                if(count($result) > 0)
                {
                    array_pop($result);
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                $result[] = $part;
            }
        }

        return count($result) ? implode("/", $result) : FALSE;
    }
}

