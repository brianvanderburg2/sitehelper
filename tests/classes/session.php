<?php

require_once(__DIR__ . "/helpers.inc");

if(!defined('MRBAVII_SITEHELPER_TEST_SERVER'))
{
    require_once("simpletest/autorun.php");
}

// Needed to prevent headers already sent errors
ob_start();

class TestSession extends UnitTestCase
{
    public function setUp()
    {
        if(!isset($_SERVER))
        {
            global $_SERVER;
            $_SERVER = array();
        }

        // Needed by Util::guid and maybe even session
        $_SERVER['HTTP_USER_AGENT'] = 'TEST';
        $_SERVER['LOCAL_ADDR'] = '127.0.0.1';
        $_SERVER['LOCAL_PORT'] = '8080';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['REMOTE_PORT'] = '16380';

        MySession::instance();
    }

    public function tearDown()
    {
    }

    public function test_session1()
    {
        MyConfig::noconfig();
        MyConfig::add(array('session' => array('driver' => 'php')));
        MyConfig::add(array('session' => array('php' => array('timed' => array('duration' => 100)))));

        $name = MySession::createTimed('100');
        MySession::setTimed($name, 100);
        $this->assertTrue(MySession::getTimed($name) == 100);

        MySession::noinstance();
        $this->assertTrue(MySession::getTimed($name) == 100);

        MySession::noinstance();
        MyConfig::noconfig();
        sleep(2);
        MyConfig::add(array('session' => array('driver' => 'php')));
        MyConfig::add(array('session' => array('php' => array('timed' => array('duration' => 0)))));

        $this->assertTrue(MySession::getTimed($name) === null);

    }
}

