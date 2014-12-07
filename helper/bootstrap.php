<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Bootstrap the helper specific features

namespace mrbavii\helper;
    
require_once(__DIR__ . '/ClassLoader.php');

// Register class loader
ClassLoader::install();
ClassLoader::register('mrbavii\\helper\\', __DIR__);

