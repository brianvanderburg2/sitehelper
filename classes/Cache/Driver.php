<?php

// File:        Driver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for connecting to a cache

namespace MrBavii\SiteHelper\Cache;

abstract class Driver
{
    public function __construct($settings) { }

    abstract public function set($name, $value, $lifetime=null);
    abstract public function get($name, $def=null);
    abstract public function remove($name);
    
    public function remember($name, $value, $lifetime=null)
    {
        $result = $this->get($name);
        if($result === null)
        {
            $this->set($name, $value, $lifetime);
            return $value;
        }
        else
        {
            return $result;
        }
    }

    public function has($name)
    {
        return ($this->get($name) !== null) ? TRUE : FALSE;
    }
}

