<?php

// File:        Connector.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for connecting to and executing queries on the database

namespace mrbavii\helper\database\connectors;
use mrbavii\helper\database;

abstract class Connector
{
    public $grammar = null;
    public $prefix = '';
    protected $settings = null;

    public function __construct($settings)
    {
        $this->settings = $settings;
        if(isset($settings['prefix']))
        {
            $this->prefix = $settings['prefix'];
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function table($name)
    {
        return new database\Table($this, $name);
    }

    public function atomic($callback)
    {
        $this->begin();
        try
        {
            $callback($this);
        }
        catch(\Exception $e)
        {
            // Catch ANY exception, because an exception in the middle will cause
            // the whole callback to stop if not handled internally
            $this->rollback();
            throw $e;
        }
            
        $this->commit();
    }

    abstract public function connect();
    abstract public function disconnect();
    abstract public function connected();

    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();

    abstract public function quote($value);
    abstract public function exec($sql); // $sql can be an array of statements
    abstract public function query($sql);
    abstract public function rowid();
}

