<?php

use MrBavii\SiteHelper\Event;

require_once("simpletest/autorun.php");
require_once(__DIR__ . "/../../bootstrap.php");

class TestEvent extends UnitTestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function test_initial()
    {
        $i0 = 0;
        $i1 = 0;

        Event::initial('initial0', function() use (&$i0) { $i0 += 2; });
        Event::initial('initial1', function() use (&$i1) { $i1 += 5; });

        Event::fire('initial0');
        $this->assertTrue($i0 == 2);
        Event::fire('initial1');
        $this->assertTrue($i1 == 5);

        Event::listen('initial0', function() use (&$i0) { $i0 += 10; });

        Event::fire('initial0');
        $this->assertTrue($i0 == 12);

        Event::clear('initial0');
        Event::fire('initial0');
        $this->assertTrue($i0 == 14);

        Event::clear('initial0');
        Event::initial('initial0', null);

        Event::fire('initial0');
        $this->assertTrue($i0 == 14);
    }

    public function test_event()
    {
        Event::initial('event0', function() { return 1010; });
        Event::listen('event0', function() { return 1; });
        Event::listen('event0', function() { return 2; });
        Event::listen('event0', function() { return 3; });
        Event::listen('event0', function() { return 4; });
        Event::listen('event0', function() { return 5; });

        $results = Event::fire('event0');
        $this->assertTrue(array_sum($results) == 15);

        Event::clear('event0');

        $results = Event::fire('event0');
        $this->assertTrue(array_sum($results) == 1010);

        Event::initial('event0', null);

        $results = Event::fire('event0');
        $this->assertTrue(count($results) == 0);
    }

    public function test_until()
    {
        $u0 = 0;

        Event::initial('until0', function() use (&$u0) { $u0 += 1; });
        Event::listen('until0', function() use (&$u0) { $u0 += 2; });
        Event::listen('until0', function() use (&$u0) { $u0 += 3; });
        Event::listen('until0', function() use (&$u0) { $u0 += 4; return 1010; });
        Event::listen('until0', function() use (&$u0) { $u0 += 5; });
        Event::listen('until0', function() use (&$u0) { $u0 += 6; });

        $results = Event::until('until0');
        $this->assertTrue($results == 1010);
        $this->assertTrue($u0 == 9);

        Event::clear('until0');
        
        $results = Event::until('until0');
        $this->assertTrue($results === null);
        $this->assertTrue($u0 == 10);
    }

    public function test_queue()
    {
        $q0 = 0;

        Event::initial('queue0', function() use (&$q0) { $q0 += 1; });
        Event::listen('queue0', function() use (&$q0) { $q0 += 2; });
        Event::listen('queue0', function() use (&$q0) { $q0 += 3; });
        Event::listen('queue0', function() use (&$q0) { $q0 += 4; return 5050; });
        Event::listen('queue0', function() use (&$q0) { $q0 += 5; });
        Event::listen('queue0', function($v1) use (&$q0) { $q0 += $v1; });

        Event::fire('queue0', array(10));
        $this->assertTrue($q0 == 24);
        
        Event::queue('queue0', array(5));
        Event::queue('queue0', array(7));
        $this->assertTrue($q0 == 24);

        Event::flush('queue0');
        $this->assertTrue($q0 == 64);

        Event::flush('queue0');
        $this->assertTrue($q0 == 64);

        Event::clear('queue0');
        Event::queue('queue0');
        Event::queue('queue0');
        Event::queue('queue0');

        Event::flush('queue0');
        $this->assertTrue($q0 == 67);

        Event::initial('queue0', null);
        Event::queue('queue0');
        Event::queue('queue0');
        Event::queue('queue0');
        
        Event::flush('queue0');
        $this->assertTrue($q0 == 67);
    }
}

