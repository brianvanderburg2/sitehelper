<?php

// File:        NullDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose;     A null cache

namespace mrbavii\helper\cache;

/**
 * \addtogroup helper_cache_drivers
 * \section helper_cache_drivers_null null
 *
 * This driver does not set or get any values.  Any attempt to set a value will silently 
 * succeed. Any attempt to get a value will return the default value.  There is no configuration
 * for this driver.
 */

class NullDriver extends Driver
{
    public function connect() { }
    public function disconnect() { }

    public function set($name, $value, $lifetime=null) {}
    public function get($name, $def=null) { return $def; }
    public function remove($name) {}
}

