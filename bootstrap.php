<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Register the classloader needed for SiteHelper

use mrbavii\helper\ClassLoader;

if(!defined("__MRBAVII_SITEHELPER__"))
{
    define("__MRBAVII_SITEHELPER__", TRUE);

    require_once(__DIR__ . '/helper/ClassLoader.php');

    ClassLoader::install();
    ClassLoader::register('mrbavii\\helper\\', __DIR__ . '/helper');
    ClassLoader::register('mrbavii\\forum\\', __DIR__ . '/forum');
}

