<?php

// File:        Connection.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Class for connecting to and executing queries on the database

namespace mrbavii\helper\database;

class Connection
{
    protected $pdo = null;
    protected $prefix = '';
    protected $error = null;

    public function __construct($settings)
    {
        if(isset($settings['prefix']))
        {
            $this->prefix = $settings['prefix'];
        }

        try
        {
            $this->pdo = new \PDO(
                $settings['dsn'],
                isset($settings['username']) ? $settings['username'] : null,
                isset($settings['password']) ? $settings['password'] : null,
                isset($settings['options']) ? $settings['options'] : null);

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            if(isset($settings['execsql']))
            {
                $this->pdo->exec($settings['execsql']);
            }
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }

    public function __destruct()
    {
        $this->pdo = null;
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

    public function begin()
    {
        try
        {
            $this->pdo->beginTransaction();
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
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
            throw new Exception($e);
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
            throw new Exception($e);
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
            throw new Exception($e);
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
            throw new Exception($e);
        }
    }

    public function prepare($sql)
    {
        try
        {
            $stmt = $this->pdo->prepare($sql);
            return new Statement($stmt);
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }
    
    public function select($sql, $params=null)
    {
        return $this->prepare($sql)->select($params);
    }

    public function insert($sql, $params=null)
    {
        return $this->prepare($sql)->insert($params);
    }

    public function update($sql, $params=null)
    {
        return $this->prepare($sql)->update($params);
    }

    public function delete($sql, $params=null)
    {
        return $this->prepare($sql)->delete($params);
    }

    public function lastInsertId($name)
    {
        try
        {
            return $this->pdo->lastInsertId($name);
        }
        catch(\PDOException $e)
        {
            throw new Exception($e);
        }
    }
}

