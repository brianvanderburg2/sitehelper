<?php

// File:        ClassLoader.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Load files containing classes on demand.

namespace mrbavii\helper;

class ClassLoader
{
    protected static $loaders = array();
    protected static $installed = FALSE;

    public static function register($ns, $dir, $ext=null)
    {
        $loader = new _ClassLoaderEntry($ns, $dir, $ext);
        static::$loaders[] = $loader;
        return $loader;
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
            if($loader->loadClass($classname))
            {
                break;
            }
        }
    }
}

class _ClassLoaderEntry
{
    protected $ns = null;
    protected $dir = null;
    protected $ext = ".php";

    public function __construct($ns, $dir, $ext=null)
    {
        $this->ns = $ns;
        $this->dir = $dir;

        if($ext !== null)
        {
            $this->ext = $ext;
        }
    }

    public function loadClass($classname)
    {
        // Check we are loading only for the desired namespace
        $len = strlen($this->ns);
        if(strlen($classname) <= $len || substr_compare($classname, $this->ns, 0, $len) != 0)
            return FALSE;

        // Remove the registered namespace portion
        $filename = $this->dir . DIRECTORY_SEPARATOR;
        $classname = substr($classname, $len);

        // Get the namespace part if any
        $pos = strrpos($classname, '\\');
        if($pos != FALSE)
        {
            $namespace = substr($classname, 0, $pos + 1);
            $classname = substr($classname, $pos + 1);

            $filename .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        // Any namespace portions are used for finding the class file
        $filename .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . $this->ext;

        // Load the file and indicate that we handled it
        require($filename);
        return TRUE;
    }
}

