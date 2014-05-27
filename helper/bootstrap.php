<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Boot strap the helper specific features

use mrbavii\helper\ClassLoader;
use mrbavii\helper\Config;
    
require_once(__DIR__ . '/ClassLoader.php');

// Register class loader
ClassLoader::install();
ClassLoader::register('mrbavii\\helper\\', __DIR__);

// Set up default configuration
Config::merge(array(
    'action.mrbavii.helper' => array(
        'listdir.callback' => '\\mrbavii\\helper\\actions\\ListDir::show'
    ),
    'route.mrbavii.helper.path' => __DIR__ . '/routes'
));

