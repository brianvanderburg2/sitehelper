<?php

use mrbavii\sitehelper\database as db;
use mrbavii\sitehelper\Database;
use mrbavii\sitehelper\Config;

require_once('simpletest/autorun.php');
require(__DIR__ . '/helpers.inc');


class TestDatabase extends UnitTestCase
{
    public function setUp()
    {
        $config = array('database' => array('connections' => array(
            'test' => array('driver' => 'sqlite3', 'prefix' => 'p'),
            'test2' => array('driver' => 'sqlite3', 'prefix' => 'p')
        )));

        Config::set($config);
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
        MyConfig::noconfig();
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
        $shouldbe = "SELECT * FROM `pusers` AS `users`";
        $this->assertTrue($sql == $shouldbe);

        $sql = $db->table('users')->getSql('a,b,c');
        $shouldbe = "SELECT `a`, `b`, `c` FROM `pusers` AS `users`";
        $this->assertTrue($sql == $shouldbe);
        
        $sql = $db->table('users')->join('groups', 'users.gid', '=', 'groups.id')->where('users.uid', '=', '100')->order('users.name')->orderDesc('users.postcount')->skip(10)->take(10)->getSql(array('users.name' => 'uname', 'groups.name' => 'gname'));
        $shouldbe = "SELECT `users`.`name` AS `uname`, `groups`.`name` AS `gname` FROM `pusers` AS `users` INNER JOIN `pgroups` AS `groups` ON `users`.`gid` = `groups`.`id` WHERE `users`.`uid` = '100' ORDER BY `users`.`name`, `users`.`postcount` DESC LIMIT 10 OFFSET 10";
        $this->assertTrue($sql == $shouldbe);

        $sql = $db->table('users')->where('name', 'like', '\\%jo_sh*')->getSql();
        $shouldbe = "SELECT * FROM `pusers` AS `users` WHERE `name` LIKE '\\\\\\%jo\\_sh%' ESCAPE '\\'";
        $this->assertTrue($sql == $shouldbe);
    }

}
