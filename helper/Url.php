<?php

namespace mrbavii\helper;

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

    public static function get($path, $key='sitehelper')
    {
        // TODO: Return the URL to the public files
        // Url::get('forum/js/forum.js') => /server/sitehelper/forum/js/forum.js
        // Base it on a configuration option like url.sitehelper or url.$key
    }
}

