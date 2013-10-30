<?php

// File:        Pdo.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Query object for PDO results

namespace mrbavii\helper\database\queries;
use mrbavii\helper\database;

class Pdo extends Query
{
    protected $stmt = null;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function __destruct()
    {
        $this->stmt->closeCursor();
        $this->stmt = null;
    }

    public function next()
    {
        try
        {
            return $this->stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('Error during fetch.', 0, $e);
        }
    }
}


