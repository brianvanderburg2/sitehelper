<?php

use MrBavii\SiteHelper\Database;

require_once('simpletest/autorun.php');
require(__DIR__ . '/../../bootstrap.php');


class TestDatabase extends UnitTestCase
{
    public function setUp()
    {
        \MrBavii\SiteHelper\Config::merge(array('test' => array('driver' => 'sqlite3', 'prefix' => 'p')), 'database.connections');
        \MrBavii\SiteHelper\Config::merge(array('test' => array('driver' => 'sqlite3', 'prefix' => 'p')), 'blah::database.connections');
    }

    public function tearDown()
    {
    }

    public function testDirect()
    {
        $db = Database::connect(array('driver' => 'sqlite3'));

        $this->assertTrue($db instanceof Database\SqliteConnection);
        $this->assertTrue($db->driver instanceof Database\Sqlite3Driver);
    }

    public function testConfig()
    {
        $db = Database::connection('test');
        $this->assertTrue($db instanceof Database\SqliteConnection);
        $this->assertTrue($db->driver instanceof Database\Sqlite3Driver);

        // Reset config and test Database cache
        \MrBavii\SiteHelper\Config::set('database.connections', array());
        $this->assertTrue(\MrBavii\SiteHelper\Config::get('database.connections') == array());

        $db2 = Database::connection('test');
        $this->assertTrue($db2 instanceof Database\SqliteConnection);
        $this->assertTrue($db2->driver instanceof Database\Sqlite3Driver);

        // Test group name
        $db3 = Database::connection('blah::test');
        
        $this->assertTrue($db3 instanceof Database\SqliteConnection);
        $this->assertTrue($db3->driver instanceof Database\Sqlite3Driver);
    }

    public function testSql()
    {
        // If previous test is run first, then this should be cached even though config is reset in previous test
        $db = Database::connection('test');

        // We use the sqlite3 driver to test the Table sql code
        $sql = $db->table('users')->get_sql();
        $this->assertTrue($sql == "SELECT * FROM `pusers` AS `users`");

        $sql = $db->table('users')->get_sql('a,b,c');
        $this->assertTrue($sql = "SELECT `a`, `b`, `c` FROM `pusers` AS `users`");

        $sql = $db->table('users')->join('groups', 'users.gid', '=', 'groups.id')->where('users.uid', '=', 100)->order('users.name')->order_desc('users.postcount')->skip(10)->take(10)->get_sql(array('users.name' => 'uname', 'groups.name' => 'gname'));
        $this->assertTrue($sql = "SELECT `users`.`name` AS `uname`, `groups`.`name` as `gname` FROM `pusers` as `users` INNER JOIN `pgroups` AS `groups` ON `users`.`gid` = `groups`.`id` WHERE `users`.`uid` = 100 ORDER BY `users`.`name`, `users`.`postcount` DESC LIMIT 10 OFFSET 10");

        $sql = $db->table('users')->where('name', 'like', '\\%jo_sh*')->get_sql();
        $this->assertTrue($sql = "SELECT * FROM `pusers` AS `users` WHERE `name` LIKE '\\\\\\%jo\\_sh%' ESCAPE '\\'");
    }

}
