<?php

use mrbavii\sitehelper\database as db;
use mrbavii\sitehelper\Database;
use mrbavii\sitehelper\Config;

require_once('simpletest/autorun.php');
require(__DIR__ . '/../../bootstrap.php');


class TestDatabase extends UnitTestCase
{
    public function setUp()
    {
        Config::merge(array('test' => array('driver' => 'sqlite3', 'prefix' => 'p')), 'database.connections');
        Config::merge(array('test2' => array('driver' => 'sqlite3', 'prefix' => 'p')), 'database.connections');
    }

    public function tearDown()
    {
    }

    public function test_direct()
    {
        $db = Database::connect(array('driver' => 'sqlite3'));

        $this->assertTrue($db instanceof db\connectors\Sqlite3);
        $this->assertTrue($db->grammar instanceof db\grammars\Sqlite);
    }

    public function test_config()
    {
        $db = Database::connection('test');
        $this->assertTrue($db instanceof db\connectors\Sqlite3);
        $this->assertTrue($db->grammar instanceof db\grammars\Sqlite);

        // Reset config and test Database cache
        Config::set('database.connections', array());
        $this->assertTrue(Config::get('database.connections') == array());

        $db2 = Database::connection('test');
        $this->assertTrue($db2 instanceof db\connectors\Sqlite3);
        $this->assertTrue($db2->grammar instanceof db\grammars\Sqlite);
    }

    public function test_sql()
    {
        // If previous test is run first, then this should be cached even though config is reset in previous test
        $db = Database::connection('test');

        // We use the sqlite3 driver to test the Table sql code
        $sql = $db->table('users')->getSql();
        $this->assertTrue($sql == "SELECT * FROM `pusers` AS `users`");

        $sql = $db->table('users')->getSql('a,b,c');
        $this->assertTrue($sql = "SELECT `a`, `b`, `c` FROM `pusers` AS `users`");

        $sql = $db->table('users')->join('groups', 'users.gid', '=', 'groups.id')->where('users.uid', '=', 100)->order('users.name')->orderDesc('users.postcount')->skip(10)->take(10)->getSql(array('users.name' => 'uname', 'groups.name' => 'gname'));
        $this->assertTrue($sql = "SELECT `users`.`name` AS `uname`, `groups`.`name` as `gname` FROM `pusers` as `users` INNER JOIN `pgroups` AS `groups` ON `users`.`gid` = `groups`.`id` WHERE `users`.`uid` = 100 ORDER BY `users`.`name`, `users`.`postcount` DESC LIMIT 10 OFFSET 10");

        $sql = $db->table('users')->where('name', 'like', '\\%jo_sh*')->getSql();
        $this->assertTrue($sql = "SELECT * FROM `pusers` AS `users` WHERE `name` LIKE '\\\\\\%jo\\_sh%' ESCAPE '\\'");
    }

}
