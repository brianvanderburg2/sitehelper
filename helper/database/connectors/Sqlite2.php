<?php

// File:        Sqlite2.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Connector for Sqlite2

namespace mrbavii\helper\database\connectors;
use mrbavii\helper\database\grammars;

class Sqlite2 extends Connector
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

        $settings['dsn'] = 'sqlite2:' . $filename;
        parent::__construct($settings);
    }
}


