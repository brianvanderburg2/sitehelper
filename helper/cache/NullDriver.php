<?php

// File:        NullDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose;     A null cache

namespace mrbavii\helper\cache;

class NullDriver extends Driver
{
    public function connect($settings) { }
    public function set($name, $value, $lifetime=null) {}
    public function get($name, $def=null) { return $def; }
    public function remove($name) {}
    public function connected() { return TRUE; }
}

