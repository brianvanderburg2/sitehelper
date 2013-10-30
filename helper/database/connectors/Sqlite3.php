<?php

// File:        Sqlite3.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Connector for Sqlite3

namespace mrbavii\helper\database\connectors;

class Sqlite3 extends Pdo
{
    public function connect($settings)
    {
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

        parent::connect($settings);
        static::exec('PRAGMA foreign_keys = ON');
    }
}


