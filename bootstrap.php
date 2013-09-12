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
    ClassLoader::register('mrbavii\\sitehelper\\', __DIR__ . DIRECTORY_SEPARATOR . 'classes');
    ClassLoader::register('mrbavii\\sitestuff\\', __DIR__ . DIRECTORY_SEPARATOR . 'stuff');
}

