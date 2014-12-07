<?php

// File:        bootstrap.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Bootstrap the apps

namespace mrbavii\apps;
use mrbavii\helper\ClassLoader;
    
// Register class loader
ClassLoader::register('mrbavii\\apps\\', __DIR__);

