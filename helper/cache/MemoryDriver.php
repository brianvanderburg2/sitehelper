<?php

// File:        MemoryCache.php
// Author:      Brian Allen Vanderburg II
// Purpose:     A simple in-memory cache.  Items are lost at startup.

namespace mrbavii\helper\cache;

/**
 * \addtogroup helper_cache_drivers
 * \section helper_cache_drivers_memory memory
 *
 * This driver stores items in a per-request cache using a PHP array.  There is no
 * configuration for this driver.
 */

class MemoryDriver extends Driver
{
    protected $data = array();

    public function connect()
    {
    }

    public function disconnect()
    {
    }
    
    public function connected()
    {
        return TRUE;
    }

    public function set($name, $value, $lifetime=null)
    {
        $this->data[$name] = $value;
    }

    public function get($name, $def=null)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $def;
    }

    public function remove($name)
    {
        unset($this->data[$name]);
    }

}

