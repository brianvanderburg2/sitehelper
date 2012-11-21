<?php

// File:        Util.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Some utility functions

namespace MrBavii\SiteHelper;

class Util
{
    public static function loadPhp($__filename__, $__params__=array(), $__require__=TRUE)
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

    public static function loadIni($filename, $params=array())
    {
        // TODO: use ini_parse and allow params to be used as constants in ini
    }
}

