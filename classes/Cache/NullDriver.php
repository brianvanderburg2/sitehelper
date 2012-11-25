<?php

// File:        NullDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose;     A null cache

namespace MrBavii\SiteHelper\Cache;

class NullDriver extends Driver
{
    public function set($name, $value, $lifetime=null) {}
    public function get($name, $def=null) { return $def; }
    public function remove($name) {}
    public function connected() { return TRUE; }
}

