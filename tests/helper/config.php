<?php

use mrbavii\helper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");


class TestConfig extends UnitTestCase
{
    public function test_config()
    {
        Config::clear();
        Config::add(array(
            'name1' => 'value1',
            'name2' => 'value2'
        ));
        Config::add(array(
            'name2' => 'value3',
            'name3' => 'value3'
        ));

        $this->assertTrue(Config::get('name1') == 'value1');
        $this->assertTrue(Config::get('name2') == 'value3');
        $this->assertTrue(Config::get('name3') == 'value3');

        $this->assertTrue(Config::all('name1') == array('value1'));
        $this->assertTrue(Config::all('name2') == array('value3', 'value2'));
        $this->assertTrue(Config::all('name3') == array('value3'));

    }
}

