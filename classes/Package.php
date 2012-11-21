<?php

// File:        Package.php
// Author:      Brian Allen Vanderburg II
// Purpose:     A simple module/package system

namespace MrBavii\SiteHelper;

class Package
{
    protected static $parsed = array();

    const MAIN = 'application';

    public static function parse($name)
    {
        if(isset(static::$parsed[$name]))
        {
            return static::$parsed[$name];
        }

        $p = explode('::', $name, 2);
        return (static::$parsed[$name] = (count($p) == 2) ? array($p[0], $p[1]) : array(static::MAIN, $p[0]));
    }
}

