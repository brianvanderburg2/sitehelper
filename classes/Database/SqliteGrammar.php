<?php

// File:        SqliteGrammar.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Grammar for Sqlite

namespace MrBavii\SiteHelper\Database;

class SqliteGrammar extends Driver
{
    abstract public function quote_column($col);
    abstract public function quote_table($table);
    abstract public function quote_value($value);
    abstract public function format_isnull($col);
    abstract public function format_isnotnull($col);
    abstract public function format_islike($col, $like);
    abstract public function format_isnotlike($col, $like);
}

