<?php

use mrbavii\helper\Config;
use mrbavii\helper\Session;

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
    }

    public function tearDown()
    {
    }

    public function test_session1()
    {
        Config::clear();
        Config::add(array(
            'session.driver' => 'php',
            'session.php' => array(
                'timed.duration' => 100
            )
        ));

        $name = Session::createTimed('100');
        Session::setTimed($name, 100);
        $this->assertTrue(Session::getTimed($name) == 100);

        // Clear session instance to test reconnection to php driver as well
        Session::instance(null);
        sleep(2);
        $this->assertTrue(Session::getTimed($name) == 100);

        Session::instance(null);
        Config::add(array(
            'session.php' => array(
                'timed.duration' => 0
            )
        ));
        sleep(2);
        $this->assertTrue(Session::getTimed($name) === null);
    }
}

