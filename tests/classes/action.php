<?php

require_once(__DIR__ . "/helpers.inc");
use mrbavii\sitehelper as sh;

if(php_sapi_name() == 'cli-server')
{
    MyConfig::noconfig();
    MyConfig::set(array('path' => array('actions' => array(__DIR__ . '/action'))));
    if(isset($_SERVER['PATH_INFO']))
    {
        sh\Action::execute($_SERVER['PATH_INFO']);
    }
    else
    {
        die("NOT FOUND");
    }
}
else
{
    require_once("simpletest/autorun.php");
    require_once("simpletest/web_tester.php");

    class TestAction extends WebTestCase
    {
        public function setUp()
        {
        }

        public function tearDown()
        {
        }

        public function test_action()
        {
            $this->get(TEST_URL . '/classes/action.php/top');
            $this->assertTitle('Top Action');

            $this->get(TEST_URL . '/classes/action.php/top2');
            $this->assertTitle('Top2 Action');
            
            $this->get(TEST_URL . '/classes/action.php/top2/data');
            $this->assertTitle('Top2 Action data');
            
            $this->get(TEST_URL . '/classes/action.php/top2/quasi');
            $this->assertTitle('Top2 Action quasi');
            
            $this->get(TEST_URL . '/classes/action.php/top2/lambda/wow');
            $this->assertTitle('Top2 Action lambda wow');
        }

    }
}

