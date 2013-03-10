<?php

use mrbavii\sitehelper\Cache;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");

class TestCache extends UnitTestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_null()
    {
        $handle = Cache::driver('null');

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === FALSE);
        $this->assertTrue($handle->get('name') === null);

        $handle = Cache::driver('memory');

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') === 100);
    }

}
