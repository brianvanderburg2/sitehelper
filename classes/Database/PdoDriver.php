<?php

// File:        PdoDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Driver for PDO

namespace MrBavii\SiteHelper\Database;

abstract class PdoDriver extends Driver
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
            // TODO: throw meaninful exception
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
            // TODO: throw
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
            // TODO: throw
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
            // TODO: throw
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
            // TODO: throw
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
            // TODO: throw
        }
    }

    public function lastrowid()
    {
        return $this->pdo->lastInsertId();
    }
}


