<?php

require_once(__DIR__ . '/../../bootstrap.php');
use mrbavii\sitehelper as sh;

// Used for some web tests
define('TEST_HOST', '127.0.0.1');
define('TEST_PORT', '8023');
define('TEST_URL', 'http://' . TEST_HOST . ':' . TEST_PORT);

// These helpers let us set or clear certain publicly invisible variables
class MyCache extends sh\Cache
{
    public static function noinstance() { static::$instance = null; }
}

class MyConfig extends sh\Config
{
    public static function noconfig() { static::$data = array(); }
}

class MyCaptcha extends sh\Captcha
{
    public static function noinstance() { static::$instance = null; }
}

class MyDatabase extends sh\Database
{
    public static function nocache() { static::$cache = array(); }
}

class MySession extends sh\Session
{
    public static function noinstance() { static::$instance = null; }
}


