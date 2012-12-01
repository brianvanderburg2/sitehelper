<?php

// File:        Table.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     Simplify database table manipulation

// This file provides a class which aids in building queries for creating and
// dropping tables as well as inserting, updating, deleting, and selecting
// data from the tables.

namespace MrBavii\SiteHelper\Database;

class Table
{
    protected $driver = null;
    protected $grammar = null;

    protected $table = null;
    protected $where_obj = null;
    protected $join_obj = null;

    public function __construct($driver, $table)
    {
        $this->driver = $driver;
        $this->grammar = $driver->grammar;

        $this->table = $table;
        $this->where_obj = new Where($this->grammar);
        $this->join_obj = new Join($this->grammar);
    }

    public function where()
    {
        $this->where_obj->handle_where(func_get_args());
        return $this;
    }

    public function or_where()
    {
        $this->where_obj->handle_or_where(func_get_args());
        return $this;
    }
}
