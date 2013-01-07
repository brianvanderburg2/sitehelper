<?php

// File:        Connector.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for connecting to and executing queries on the database

namespace mrbavii\sitehelper\database\connectors;
use mrbavii\sitehelper\database;

abstract class Connector
{
    public $grammar = null;
    public $prefix = '';

    public function __construct($settings, $gfactory)
    {
        if($gfactory instanceof \Closure)
        {
            $this->grammar = $gfactory($this);
        }
        else
        {
            $this->grammar = new $gfactory($this);
        }

        if(isset($settings['prefix']))
        {
            $this->prefix = $settings['prefix'];
            unset($settings['prefix']);
        }

        $this->connect($settings);
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

    abstract public function connect($settings);
    abstract public function disconnect();

    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();

    abstract public function quote($value);
    abstract public function exec($sql);
    abstract public function query($sql);
    abstract public function rowid();
}

