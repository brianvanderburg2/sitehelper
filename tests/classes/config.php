<?php

use mrbavii\sitehelper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");


class TestConfig extends UnitTestCase
{
    public function setUp()
    {
        Config::group('application', array('name1' => 1000, 'name2' => array('a' => 1, 'b' => 2)));
        Config::group('user', array('name1' => 4000, 'name3' => 6000));
    }

    public function tearDown()
    {
    }

    public function test_get()
    {
        $this->assertTrue(Config::get('name1') == 4000);
        $this->assertTrue(Config::get('name1', null, 'user') == 4000);
        $this->assertTrue(Config::get('name1', null, 'application') == 1000);
        $this->assertTrue(Config::get('name1', null, 'application,user') == 1000);

        $this->assertTrue(Config::get('name2') == array('a' => 1, 'b' => 2));
        $this->assertTrue(Config::get('name2.a') == 1);
        $this->assertTrue(Config::get('name2.b') == 2);
        $this->assertTrue(Config::get('name2', null, 'user') == null);
        $this->assertTrue(Config::get('name2', null, 'application') == array('a' => 1, 'b' => 2));
        $this->assertTrue(Config::get('name2', null, 'application,user') == array('a' => 1, 'b' => 2));

        $this->assertTrue(Config::get('name3') == 6000);
        $this->assertTrue(Config::get('name3', null, 'user') == 6000);
        $this->assertTrue(Config::get('name3', 700, 'application') == 700);
    }

    public function test_parse()
    {
        $p1 = 'host=test.tld,port=100,user=me,pass=secret';
        $p1r = array('host' => 'test.tld',
                     'port' => '100',
                     'user' => 'me',
                     'pass' => 'secret');

        $p2 = 'host:test.tld;host:test2.tld;user:me;user:me2;retry:5';
        $p2r = array('host' => array('test.tld', 'test2.tld'),
                     'user' => array('me', 'me2'),
                     'retry' => '5');

        $this->assertTrue(Config::parse($p1, ',', '=') == $p1r);
        $this->assertTrue(Config::parse($p2, ';', ':') == $p2r);
    }
}
