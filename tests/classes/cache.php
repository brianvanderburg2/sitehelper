<?php

use mrbavii\sitehelper\Cache;
use mrbavii\sitehelper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");

class TestCache extends UnitTestCase
{
    public function setUp()
    {
        Config::clear();
        Config::add(array('cache' => array('driver' => 'memory')));
    }

    public function tearDown()
    {
    }

    public function test_instance()
    {
        $handle = Cache::instance();

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') == 100);
    }

    public function test_null()
    {
        $settings = array('driver' => 'null');

        $handle = Cache::connect($settings);

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === FALSE);
        $this->assertTrue($handle->get('name') === null);
    }

    public function test_memory()
    {
        $settings = array('driver' => 'memory');

        $handle = Cache::connect($settings);
        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') == 100);
    }

    public function test_direct()
    {
        Cache::set('name', 'Bob');
        Cache::set('age', 21);

        $this->assertTrue(Cache::has('name') == TRUE);
        $this->assertTrue(Cache::get('name') == 'Bob');
        
        $this->assertTrue(Cache::has('age') == TRUE);
        $this->assertTrue(Cache::get('age') == 21);
        
        $this->assertTrue(Cache::has('ssn') == FALSE);
    }

}
