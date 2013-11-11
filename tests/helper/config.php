<?php

use mrbavii\helper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");


class TestConfig extends UnitTestCase
{
    public function setUp()
    {
        Config::set(array('name1' => 1000, 'name2' => array('a' => 1, 'b' => 2)));
        Config::merge(array('name2.c' => 4001, 'name2.d' => 4002, 'name2.e' => array('f.g' => 31)));
    }

    public function tearDown()
    {
    }

    public function test_get()
    {
        $this->assertTrue(Config::get('name1') == 1000);

        $this->assertTrue(Config::get('name2') == array('a' => 1, 'b' => 2, 'c' => 4001, 'd' => 4002, 'e' => array('f' => array('g' => 31))));
        $this->assertTrue(Config::get('name2.a') == 1);
        $this->assertTrue(Config::get('name2.b') == 2);
        $this->assertTrue(Config::get('name2.c') == 4001);
        $this->assertTrue(Config::get('name2.d') == 4002);
        $this->assertTrue(Config::get('name2.e.f.g') == 31);
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

