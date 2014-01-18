<?php

use mrbavii\helper\Cache;
use mrbavii\helper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/helpers.inc");

class TestCache extends UnitTestCase
{
    public function setUp()
    {
        Config::merge(array('cache' => array('driver' => 'memory')));
    }

    public function tearDown()
    {
    }

    public function test_instance()
    {
        $handle = MyCache::instance();

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') == 100);
    }

    public function test_null()
    {
        $settings = array('driver' => 'null');

        $handle = MyCache::connect($settings);

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === FALSE);
        $this->assertTrue($handle->get('name') === null);
    }

    public function test_memory()
    {
        $settings = array('driver' => 'memory');

        $handle = MyCache::connect($settings);
        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') == 100);
    }

    public function test_direct()
    {
        MyCache::set('name', 'Bob');
        MyCache::set('age', 21);

        $this->assertTrue(Cache::has('name') == TRUE);
        $this->assertTrue(Cache::get('name') == 'Bob');
        
        $this->assertTrue(Cache::has('age') == TRUE);
        $this->assertTrue(Cache::get('age') == 21);
        
        $this->assertTrue(Cache::has('ssn') == FALSE);
    }

}
