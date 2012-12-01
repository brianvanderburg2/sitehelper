<?php

// File:        PdoDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Driver for Sqlite3

namespace MrBavii\SiteHelper\Database;

class Sqlite3Driver extends PdoDriver
{
    public function __construct($settings)
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

        parent::__construct($settings);
    }
}


