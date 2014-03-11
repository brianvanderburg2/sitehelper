<?php

// File:        Connector.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for connecting to and executing queries on the database

namespace mrbavii\helper\database\connectors;
use mrbavii\helper\database;

abstract class Connector
{
    protected $settings = null;
    protected $pdo = null;
    protected $grammar = null;
    protected $prefix = '';

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

    public function prefix()
    {
        return $this->prefix;
    }

    public function grammar()
    {
        return $this->grammar;
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

    public function connect()
    {
        if($this->pdo !== null)
            return $this->pdo;

        $settings = $this->settings;

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

    public function prepare($sql)
    {
        try
        {
            $stmt = $this->pdo->prepare($sql);
            return new database\queries\Query($stmt, $this->pdo);
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL query error.', 0, $e);
        }
    }
    
    public function select($sql, $params=null)
    {
        $stmt = $this->prepare($sql);
        $stmt->exec($params);
        return $stmt;
    }

    public function insert($sql, $params=null)
    {
        $stmt = $this->prepare($sql);
        $stmt->exec($params);
        return $stmt->rowCount();
    }

    public function update($sql, $params=null)
    {
        $stmt = $this->prepare($sql);
        $stmt->exec($params);
        return $stmt->rowCount();
    }

    public function delete($sql, $params=null)
    {
        $stmt = $this->prepare($sql);
        $stmt->exec($params);
        return $stmt->rowCount();
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
            return $this->pdo->errorCode();
        }
        catch(\PDOException $e)
        {
            throw new database\Exception('SQL errorCode error.', 0, $e);
        }
    }
}

