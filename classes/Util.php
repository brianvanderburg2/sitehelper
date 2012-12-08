<?php

// File:        Util.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Some utility functions

namespace MrBavii\SiteHelper;

class Util
{
    public static function load_php($__filename__, $__params__=array(), $__require__=TRUE)
    {
        extract($__params__);
        if($__require__)
        {
            return require $__filename__;
        }
        else
            return include $__filename__;
        }
    }

    public static function load_ini($filename, $params=array())
    {
        // TODO: use ini_parse and allow params to be used as constants in ini
    }

    public static function guid($namespace='')
    {
        // TODO: use uuid_create also if available
        if(function_exists('com_create_guid'))
        {
            return trim(com_create_guid(), '{}');
        }
        else
        {
            return static::internal_guid($namespace);
        }
    }

    public static function internal_guid($namespace='')
    {
        // This code comes from http://php.net/manual/en/function.uniqid.php
        // Curly brackets are not included
        static $guid = '';

        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['LOCAL_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];

        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash,  0,  8) .
            '-' .
            substr($hash,  8,  4) .
            '-' .
            substr($hash, 12,  4) .
            '-' .
            substr($hash, 16,  4) .
            '-' .
            substr($hash, 20, 12);

        return $guid;
    }
}

