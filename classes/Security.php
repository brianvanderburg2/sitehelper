<?php

// File:        Security.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Provide some simple security related functions

namespace mrbavii\sitehelper;

class Security
{
    public static function checkPathComponent($part)
    {
        return (bool)preg_match("#^[a-zA-Z0-9][a-zA-Z0-9_\\.]*$#", $part);
    }
}

