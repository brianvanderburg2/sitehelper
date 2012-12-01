<?php

// File:        Grammar.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for describing the grammar for particular databases

namespace MrBavii\SiteHelper\Database;

abstract class Grammar
{
    protected $driver;

    public function __construct($driver, $settings)
    {
        $this->driver = $driver;
    }

    public function __destruct()
    {
        $this->driver = null;
    }

    public function table($name)
    {
        return new Table($this, $name);
    }

    public function atomic($callback)
    {
        $this->driver->begin();
        try
        {
            $callback($this);
            $this->driver->commit();
        }
        catch(Exception $e)
        {
            $this->driver->rollback();
            throw;
        }
    }

    abstract public function quote_column($col);
    abstract public function quote_table($table);
    abstract public function quote_value($value);
    abstract public function format_isnull($col);
    abstract public function format_isnotnull($col);
    abstract public function format_islike($col, $like);
    abstract public function format_isnotlike($col, $like);
}

