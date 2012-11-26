<?php

// File:        ClassLoader.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Load files containing classes on demand.

namespace MrBavii\SiteHelper;

class ClassLoader
{
    protected static $loaders = array();
    protected static $installed = FALSE;

    public static function register($ns, $dir, $sep=null, $ext=null)
    {
        $loader = new _ClassLoaderEntry($ns, $dir, $sep, $ext);
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
    protected $sep = "\\";
    protected $ext = ".php";

    public function __construct($ns, $dir, $sep=null, $ext=null)
    {
        $this->ns = $ns;
        $this->dir = $dir;

        if($sep !== null)
        {
            $this->sep = $sep;
        }

        if($ext !== null)
        {
            $this->ext = $ext;
        }
    }

    public function loadClass($classname)
    {
        // Check we are loading only for the desired namespace
        $check = $this->ns . $this->sep;
        if(strlen($classname) <= strlen($check) || substr_compare($classname, $check, 0, strlen($check)) != 0)
            return FALSE;

        // Remove the registered namespace portion
        $filename = $this->dir . DIRECTORY_SEPARATOR;
        $classname = substr($classname, strlen($check));

        // Any remaining namespace portions are used for finding the directory
        $pos = strripos($classname, $this->sep);
        if($pos !== FALSE)
        {
            $namespace = substr($classname, 0, $pos);
            $classname = substr($classname, $pos + 1);
            $filename .= str_replace($this->sep, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        // Add class name and extension
        $filename .= $classname . $this->ext;

        // Load the file and indicate that we handled it
        require($filename);
        return TRUE;
    }
}

