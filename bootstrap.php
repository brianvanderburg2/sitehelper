<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Register the classloader needed for SiteHelper

namespace mrbavii\sitehelper;

if(!defined(__NAMESPACE__."\\BOOTSTRAPPED"))
{
    define(__NAMESPACE__."\\BOOTSTRAPPED", TRUE);

    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'ClassLoader.php');

    ClassLoader::install();
    ClassLoader::register(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes');
}

