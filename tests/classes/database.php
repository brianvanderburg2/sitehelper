<?php

use MrBavii\SiteHelper\Database;

require_once('simpletest/autorun.php');
require(__DIR__ . '/../../bootstrap.php');

class TestDatabase extends UnitTestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testDirect()
    {
        $db = Database::connect(array('driver' => 'sqlite3'));

        $this->assertTrue($db instanceof Database\SqliteConnection);
    }

}
