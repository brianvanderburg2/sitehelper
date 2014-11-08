<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Boot strap the helper specific features

namespace mrbavii\helper;
    
require_once(__DIR__ . '/ClassLoader.php');

// Register class loader
ClassLoader::install();
ClassLoader::register('mrbavii\\helper\\', __DIR__);

// Set up default configuration
Route::register(
    '/mrbavii.helper/listdir',
    __NAMESPACE__ . '\routes\ListDir::show',
    'mrbavii.helper.listdir'
);

Route::register(
    '/mrbavii.helper/error',
    __NAMESPACE__ . '\routes\Error::show',
    'mrbavii.helper.listdir'
);

Template::addSearchPath(__DIR__ . '/templates', 'mrbavii.helper');

