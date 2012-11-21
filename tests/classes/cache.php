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

    public function testNull()
    {
        $handle = Cache::driver('null');

        $handle->set('name', 100);
        $this->assertTrue($handle->has('name') === FALSE);
        $this->assertTrue($handle->get('name') === null);
    }

}
