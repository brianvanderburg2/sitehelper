<?php

// File:        Grammar.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for formatting and syntax to the database

namespace MrBavii\SiteHelper\Database\Grammars;

abstract class Grammar
{
    public $connector;

    public function __construct($connector)
    {
        $this->connector = $connector;
    }

    public function __destruct()
    {
        $this->connector = null;
    }


    // There are not used publicly, only by Table
    abstract public function quote_column($col);
    abstract public function quote_table($table);
    abstract public function quote_value($value);
    abstract public function format_isnull($col);
    abstract public function format_isnotnull($col);
    abstract public function format_islike($col, $like);
    abstract public function format_isnotlike($col, $like);
    abstract public function format_limit($limit, $offset);
    abstract public function format_create_table($table, $columns, $constraints);

}

