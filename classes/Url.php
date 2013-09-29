<?php

namespace mrbavii\sitehelper;

class Url
{
    public static function isAbsolute($url)
    {
        if(substr($url, 0, 1) == '/')
        {
            return TRUE;
        }

        if(strpos($url, ':') !== FALSE)
        {
            return TRUE;
        }

        return FALSE;
    }

    public static function isRelative($url)
    {
        return !static::isAbsolute($url);
    }

    public static function redirect($url, $code=303)
    {
        return Response::redirect($url, $code);
    }
}

