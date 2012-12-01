<?php

// File:        PdoDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Driver for PDO

namespace MrBavii\SiteHelper\Database;

class PdoDriver extends Driver
{
    protected $pdo = null;

    public function __construct($settings)
    {
        parent::__construct($settings);

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
            throw new Exception('Error connecting to database.', 0, $e);
        }
    }

    public function begin()
    {
        try
        {
            $this->pdo->beginTransaction();
        }
        catch(\PDOException $e)
        {
            throw new Exception('Transaction error.', 0, $e);
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
            throw new Exception('Transaction error.', 0, $e);
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
            throw new Exception('Transaction error.', 0, $e);
        }
    }
    
    public function exec($sql)
    {
        try
        {
            $this->pdo->exec($sql);
        }
        catch(\PDOException $e)
        {
            throw new Exception('SQL execution error.', 0, $e);
        }
    }

    public function query($sql)
    {
        try
        {
            $stmt = $this->pdo->query($sql);
            return new PdoQuery($stmt);
        }
        catch(\PDOException $e)
        {
            throw new Exception('SQL query error.', 0, $e);
        }
    }

    public function lastrowid()
    {
        try
        {
            return $this->pdo->lastInsertId();
        }
        catch(\PDOException $e)
        {
            throw new Exception('Last row id error.', 0, $e);
        }
    }
}


