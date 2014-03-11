<?php

// File:        Query.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for a result from a query

namespace mrbavii\helper\database\queries;
use mrbavii\helper\database;

abstract class Query
{
    protected $stmt = null;

    public function __construct($stmt, $pdo)
    {
        $this->stmt = $stmt;
        $this->pdo = $pdo;
    }

    public function __destruct()
    {
        $this->stmt->closeCursor();
        $this->stmt = null;
    }

    public function close()
    {
        $this->stmt->closeCursor()
    }

    protected function exec($params)
    {
        try
        {
            return $this->stmt->execute($params);
        }
        catch(\PDOException $e)
        {
            throw new database\Exception("Error executing prepared statement.", 0, $e);
        }
    }

    public function select($params=null)
    {
        return $this->exec($params);
    }

    public function insert($params=null)
    {
        $this->exec($params);
        return $this->rowCount();
    }

    public function update($params=null)
    {
        $this->exec($params);
        return $this->rowCount();
    }

    public function delete($params=null)
    {
        $this->exec($params);
        return $this->rowCount();
    }

    public function rowid()
    {
        try
        {
            return $this->pdo->lastInsertId();
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL last inert id error.', 0, $e);

        }
    }

    public function errorCode()
    {
        try
        {
            return $this->stmt->errorCode();
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL errorCode error.', 0, $e);
        }
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


