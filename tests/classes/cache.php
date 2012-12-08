<?php

use MrBavii\SiteHelper\Cache;

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

        $handle = Cache::driver('group2::memory');

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') === 100);

        $handle = Cache::driver('group2::memory');

        $this->assertTrue($handle->has('name') === TRUE);
        $this->assertTrue($handle->get('name') === 100);

        // This is a different 'connection' to the same driver.  Each memory driver
        // has it's own cache so each one is different.
        $handle = Cache::driver('Package1::memory');

        $this->assertTrue($handle->has('name') === FALSE);

    }

}
