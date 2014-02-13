<?php

// File:        Sqlite3.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Connector for Sqlite3

namespace mrbavii\helper\database\connectors;
use mrbavii\helper\database\grammars;

class Sqlite3 extends Pdo
{
    public function __construct($settings)
    {
        $this->grammar = new grammars\Sqlite($this);

        if(isset($settings['filename']))
        {
            $filename = $settings['filename'];
            unset($settings['filename']);
        }
        else
        {
            $filename = ':memory:';
        }

        $settings['dsn'] = 'sqlite:' . $filename;
        parent::__construct($settings);
    }

    public function connect()
    {
        parent::connect();
        static::exec('PRAGMA foreign_keys = ON');
    }
}


