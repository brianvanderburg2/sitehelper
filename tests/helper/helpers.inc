<?php

require_once(__DIR__ . '/../../bootstrap.php');
use mrbavii\helper;

// Used for some web tests
define('TEST_HOST', '127.0.0.1');
define('TEST_PORT', '8023');
define('TEST_URL', 'http://' . TEST_HOST . ':' . TEST_PORT);

// These helpers let us set or clear certain publicly invisible variables
class MyCache extends helper\Cache
{
    public static function noinstance() { static::$instance = null; }
}

class MyCaptcha extends helper\Captcha
{
    public static function noinstance() { static::$instance = null; }
}

class MyDatabase extends helper\Database
{
    public static function nocache() { static::$cache = array(); }
}

class MySession extends helper\Session
{
    public static function noinstance() { static::$instance = null; }
}


