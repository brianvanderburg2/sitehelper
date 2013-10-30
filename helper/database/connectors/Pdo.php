<?php

// File:        Pdo.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Connector for PDO

namespace mrbavii\helper\database\connectors;
use mrbavii\helper\database;

class Pdo extends Connector
{
    protected $pdo = null;

    public function connect($settings)
    {
        try
        {
            $this->pdo = new \PDO(
                $settings['dsn'],
                isset($settings['username']) ? $settings['username'] : null,
                isset($settings['password']) ? $settings['password'] : null,
                isset($settings['options']) ? $settings['options'] : null);

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            if(isset($settings['execsql']))
            {
                $this->pdo->exec($settings['execsql']);
            }
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('Error connecting to database.', 0, $e);
        }
    }

    public function disconnect()
    {
        $this->pdo = null;
    }

    public function begin()
    {
        try
        {
            $this->pdo->beginTransaction();
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('Transaction error.', 0, $e);
        }
    }

    public function commit()
    {
        try
        {
            $this->pdo->commit();
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('Transaction error.', 0, $e);
        }
    }

    public function rollback()
    {
        try
        {
            $this->pdo->rollBack();
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('Transaction error.', 0, $e);
        }
    }
    
    public function quote($value)
    {
        try
        {
            return $this->pdo->quote($value);
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL quote error.', 0, $e);
        }
    }

    public function exec($sql)
    {
        try
        {
            if(is_array($sql))
            {
                foreach($sql as $s)
                {
                    $this->pdo->exec($s);
                }
            }
            else
            {
                $this->pdo->exec($sql);
            }
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL execution error.', 0, $e);
        }
    }

    public function query($sql)
    {
        try
        {
            $stmt = $this->pdo->query($sql);
            return new database\queries\Pdo($stmt);
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL query error.', 0, $e);
        }
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
}


