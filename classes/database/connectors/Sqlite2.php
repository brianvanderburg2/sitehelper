<?php

// File:        Sqlite2.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Connector for Sqlite3

namespace mrbavii\sitehelper\database\connectors;

class Sqlite2 extends Pdo
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

        $settings['dsn'] = 'sqlite2:' . $filename;

        parent::connect($settings);
    }
}


