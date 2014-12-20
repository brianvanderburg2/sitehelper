<?php

// File:        Statement.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Class for a handling prepared queries

namespace mrbavii\helper\database;

class Statement
{
    protected $stmt = null;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function __destruct()
    {
        $this->stmt = null;
    }

    public function exec($params)
    {
        try
        {
            $this->stmt->execute($this->params($params));
            return new ResultSet($this->stmt);
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }

    public function select($params=null)
    {
        return $this->exec($params);
    }

    public function insert($params=null)
    {
        return $this->exec($params);
    }

    public function update($params=null)
    {
        return $this->exec($params);
    }

    public function delete($params=null)
    {
        return $this->exec($params);
    }

    protected function params($params)
    {
        // String keys need to have a ':' prepended
        foreach($params as $key => $value)
        {
            if(!is_int($key) && substr($key, 0, 1) != ':')
            {
                $params[':' . $key] = $value;
            }
        }
        return $params;
    }
}


