<?php

// File:        Statement.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Class for a handling prepared queries

namespace mrbavii\helper\database;

class Statement
{
    protected $stmt = null;
    protected $pdo = null;

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
        $this->stmt->closeCursor();
    }

    public function exec($params)
    {
        return $this->stmt->execute($params);
    }

    public function select($params=null)
    {
        return $this->exec($params);
    }

    public function insert($params=null)
    {
        $this->exec($params);
        return $this->stmt->rowCount();
    }

    public function update($params=null)
    {
        $this->exec($params);
        return $this->stmt->rowCount();
    }

    public function delete($params=null)
    {
        $this->exec($params);
        return $this->stmt->rowCount();
    }

    public function rowid($name=null)
    {
        return $this->pdo->lastInsertId($name);
    }

    public function errorCode()
    {
        return $this->stmt->errorCode();
    }

    public function next()
    {
        return $this->stmt->fetch(\PDO::FETCH_BOTH);
    }
}


