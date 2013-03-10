<?php

use mrbavii\sitehelper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");


class TestConfig extends UnitTestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_set_get()
    {
        Config::set('testc1', 100);
        Config::set('testc2', array(1, 2, 3));
        Config::set('testc3', 10);
        Config::set('testc4', 20);


        $this->assertTrue(Config::get('testc2') == array(1, 2, 3));
        $this->assertTrue(Config::get('testc3') == 10);
        $this->assertTrue(Config::get('testc4') == 20);

        $this->assertTrue(Config::get('testc5', 500) == 500);
        $this->assertTrue(Config::get('testc5', 600) == 600);

        Config::set('a.b.c', 1000);
        Config::set('a.b.d', 2000);

        $this->assertTrue(Config::get('a.b.c') == 1000);
        $this->assertTrue(Config::get('a.b.d') == 2000);
        $this->assertTrue(Config::get('a.b') == array('c' => 1000, 'd' => 2000));

        Config::set('a.b', 12);
        
        $this->assertTrue(Config::get('a.b.c') === null);
        $this->assertTrue(Config::get('a.b.d') === null);
        $this->assertTrue(Config::get('a.b') == 12);

        Config::set('a.b.e', 2020);

        $this->assertTrue(Config::get('a.b') == array('e' => 2020));
        $this->assertTrue(Config::get('a.b.e') == 2020);
    }

    public function test_merge()
    {
        Config::set('d.e.f', 100);
        Config::set('d.e.g', 200);

        Config::merge(array('h' => 300, 'i' => 400), 'd.e');
        Config::merge(array('f' => 600), 'd');

        $this->assertTrue(Config::get('d.e') == array('f' => 100, 'g' => 200, 'h' => 300, 'i' => 400));
        $this->assertTrue(Config::get('d.f') == 600);

        Config::merge(array('e' => array('f' => 500)), 'd');

        $this->assertTrue(Config::get('d.e') == array('f' => 500));
        $this->assertTrue(Config::get('d.f') == 600);
        
        Config::set('d', 0);
        Config::set('d.e.f', 100);
        Config::set('d.e.g', 200);

        Config::mergeRecursive(array('h' => 300, 'i' => 400), 'd.e');
        Config::mergeRecursive(array('f' => 600), 'd');

        $this->assertTrue(Config::get('d.e') == array('f' => 100, 'g' => 200, 'h' => 300, 'i' => 400));
        $this->assertTrue(Config::get('d.f') == 600);

        Config::mergeRecursive(array('e' => array('f' => 500)), 'd');

        $this->assertTrue(Config::get('d.e') == array('f' => 500, 'g' => 200, 'h' => 300, 'i' => 400));
        $this->assertTrue(Config::get('d.f') == 600);
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
