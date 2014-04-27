<?php

use mrbavii\helper\Template;
use mrbavii\helper\Config;

require_once('simpletest/autorun.php');
require_once(__DIR__ . '/../../bootstrap.php');

class TestTemplate extends UnitTestCase
{
    public function setUp()
    {
        Config::merge(array(
            'template.test' => array(__DIR__ . '/template/1'),
        ));
        Config::merge(array(
            'template.test' => array(__DIR__ . '/template/2'),
        ));
    }

    public function tearDown()
    {
    }

    public function test_template()
    {
        $result = Template::get('test', 'test1', array('case' => $this, 'number' => 500));
        $result = str_replace(array(" ", "\t", "\r", "\n", "\0"), "", $result);

        $this->assertTrue($result == "abc123error456shouldgetheredef");
    }
}
