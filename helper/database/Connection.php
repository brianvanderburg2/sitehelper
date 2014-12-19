<?php

// File:        Connection.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Class for connecting to and executing queries on the database

namespace mrbavii\helper\database;

class Connection
{
    protected $settings = null;
    protected $pdo = null;
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

    public function prefix()
    {
        return $this->prefix;
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

    public function disconnect()
    {
        $this->pdo = null;
    }

    public function begin()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollback()
    {
        $this->pdo->rollBack();
    }
    
    public function quote($value)
    {
        return $this->pdo->quote($value);
    }

    public function exec($sql)
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

    public function prepare($sql)
    {
        $stmt = $this->pdo->prepare($sql);
        return new Statement($stmt, $this->pdo);
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
        return $this->pdo->lastInsertId();
    }

    public function errorCode()
    {
        return $this->pdo->errorCode();
    }
}

