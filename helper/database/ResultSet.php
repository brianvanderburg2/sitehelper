<?php

// File:        ResultSet.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Class for a handling results

namespace mrbavii\helper\database;

class ResultSet
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

    public function close()
    {
        try
        {
            $this->stmt->closeCursor();
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }

    public function fetch($default=FALSE)
    {
        try
        {
            $result = $this->stmt->fetch();
            if(is_array($result))
            {
                return $result;
            }
            return $default;
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }

    public function fetchall()
    {
        try
        {
            return $this->stmt->fetchAll();
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }

    public function rowCount()
    {
        try
        {
            return $this->stmt->rowCount();
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }
}


