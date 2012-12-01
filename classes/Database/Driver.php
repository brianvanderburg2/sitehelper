<?php

// File:        Driver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for connecting to and executing queries on the database

namespace MrBavii\SiteHelper\Database;

abstract class Driver
{
    public function __construct($settings)
    {
    }

    public function __destruct()
    {
    }

    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();

    abstract public function exec();
    abstract public function query();
}

