<?php

use mrbavii\helper\Route;
use mrbavii\helper\Config;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/helpers.inc");

class TestRoute extends UnitTestCase
{
    protected static $testkey = null;
    public function setUp()
    {
        Config::merge(array(
            'route.mrbavii.helper.test.route1.config' => array(
                'c1' => 100,
                'c2' => 200
            ),
            'route.mrbavii.helper.test.route2.config' => array(
                'c1' => 300,
                'c2' => 600,
                'c3' => 900
            )
        ));
        
        Route::register('/mrbavii.helper/test.route1/p1-{p1:num}/p2-{p2:alpha}/p3-{p3:alnum}/p4-{p4:ident}/p5-{p5:any}/{p6:all}',
                        array($this, 'route1'),
                        'mrbavii.helper.test.route1');
        Route::register('/mrbavii.helper/test.route2/p1-{p1:alpha}/p2-{p2:all}',
                        'TestRoute::route2',
                        'mrbavii.helper.test.route2');
    }

    public function tearDown()
    {
    }

    public function route1($params, $config)
    {
        $this->assertTrue($config == array('c1' => 100, 'c2' => 200));
        static::$testkey = $params;
    }

    public static function route2($params, $config)
    {
        $this->assertTrue($config == array('c1' => 300, 'c2' => 600, 'c3' => '900'));
        static::$testkey = $params;
    }

    public function test_routes()
    {
        // Test PATH_INFO dispatch first
        $old = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : FALSE;
        $_SERVER['PATH_INFO'] = '/mrbavii.helper/test.route1/p1-23/p2-abcd/p3-abc123/p4-user_name/p5-we.need-food.today/and/also%2Fmoney';
        
        Route::dispatch();

        if($old == FALSE)
            unset($_SERVER['PATH_INFO']);
        else
            $_SERVER['PATH_INFO'] = $old;

        $this->assertTrue(static::$testkey['p1'] == '23');
        $this->assertTrue(static::$testkey['p2'] == 'abcd');
        $this->assertTrue(static::$testkey['p3'] == 'abc123');
        $this->assertTrue(static::$testkey['p4'] == 'user_name');
        $this->assertTrue(static::$testkey['p5'] == 'we.need-food.today');
        $this->assertTrue(static::$testkey['p6'] == 'and/also%2Fmoney');

        // Route does not exist
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route3'));

        // Invalid route parameters
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route1/p1-12z3/p2-abcd/p3-abc123/p4-user_name/p5-we.need-food.today/and//also/money'));
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route1/p1-123/p2-abcd1/p3-abc123/p4-user_name/p5-we.need-food.today/and//also/money'));
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route1/p1-123/p2-abcd/p3-_abc123/p4-user_name/p5-we.need-food.today/and//also/money'));
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route1/p1-123/p2-abcd/p3-abc123/p4-user_name./p5-we.need-food.today/and//also/money'));
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route1/p1-123/p2-abcd/p3-abc123/p4-user_name/p5-we.need-food.today'));
        $this->assertFalse(Route::dispatch('/mrbavii.helper/test.route1/p1-123/p2-abcd/p3-abc123/p4-user_name/p5-'));
        
        // Valid routes
        $this->assertTrue(Route::dispatch('/mrbavii.helper/test.route1/p1-123/p2-abcd/p3-abc123/p4-user_name/p5-we.need-food.today/'));
        $this->assertTrue(static::$testkey['p6'] == '');
    }

    public function test_urls()
    {
        $url = Route::url('mrbavii.helper.test.route1', array('p1' => 230, 'p2' => 'abcdef', 'p3' => 'crazy4u', 'p4' => 'misc_total', 'p5' => 'we can do this', 'p6' => 'tools/info.php'));
        $this->assertTrue($url == $_SERVER['SCRIPT_NAME'] . '/mrbavii.helper/test.route1/p1-230/p2-abcdef/p3-crazy4u/p4-misc_total/p5-we+can+do+this/tools%2Finfo.php');
    }
}
