<?php

// File:        MemoryCache.php
// Author:      Brian Allen Vanderburg II
// Purpose:     A simple in-memory cache.  Items are lost at startup.

namespace mrbavii\sitehelper\cache;

class MemoryDriver extends Driver
{
    protected $data = array();

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

    public function connected()
    {
        return TRUE;
    }
}

