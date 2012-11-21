<?php

// File:        ClassLoader.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Load files containing classes on demand.

namespace MrBavii\SiteHelper;

class ClassLoader
{
    protected static $loaders = array();
    protected static $installed = FALSE;

    public static function register($ns, $dir, $sep="\\", $ext=".php")
    {
        static::$loaders[] = array($ns, $dir, $sep, $ext); 
    }

    public static function install()
    {
        if(!static::$installed)
        {
            static::$installed = TRUE;
            spl_autoload_register(array(__NAMESPACE__."\\ClassLoader", 'loadClass'));
        }
    }

    public static function loadClass($classname)
    {
        foreach(static::$loaders as $loader)
        {
            list($ns, $dir, $sep, $ext) = $loader;

            // Check we are loading only for the desired namespace
            $check = $ns . $sep;
            if(strlen($classname) <= strlen($check) || substr_compare($classname, $check, 0, strlen($check)) != 0)
                continue;

            // Remove the registered namespace portion
            $filename = $dir . DIRECTORY_SEPARATOR;
            $classname = substr($classname, strlen($check));

            // Any remaining namespace portions are used for finding the directory
            $pos = strripos($classname, $sep);
            if($pos !== FALSE)
            {
                $namespace = substr($classname, 0, $pos);
                $classname = substr($classname, $pos + 1);
                $filename .= str_replace($sep, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            // Add class name and extension
            $filename .= $classname . $ext;

            // Require even if it doesn't exist
            require($filename);
            break;
        }
    }
}



