<?php

// File:        Grammar.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for formatting and syntax to the database

namespace mrbavii\helper\database\grammars;

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
    abstract public function quoteColumn($col);
    abstract public function quoteTable($table);
    abstract public function quoteValue($value);
    abstract public function formatNull($col);
    abstract public function formatNotNull($col);
    abstract public function formatLike($col, $like);
    abstract public function formatNotLike($col, $like);
    abstract public function formatLimit($limit, $offset);
    abstract public function formatCreateTable($table, $columns, $constraints, $ifnotexists);

}

