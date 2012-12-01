<?php

// File:        Table.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     Simplify database table manipulation

// This file provides a class which aids in building queries for creating and
// dropping tables as well as inserting, updating, deleting, and selecting
// data from the tables.

namespace MrBavii\SiteHelper\Database;

class Table
{
    protected $connection = null;
    protected $driver = null;
    protected $prefix = '';
    protected $table = null;

    protected $joins = array();
    protected $where_obj = null;
    protected $ordered = array();

    protected $offset = null;
    protected $limit = null;

    public function __construct($connection, $table)
    {
        $this->connection = $connection;
        $this->driver = $connection->driver;
        $this->prefix = $connection->prefix;
        $this->table = $table;

        $this->where_obj = new Where($connection);
    }
    
    public function join($table)
    {
        $args = func_get_args();
        array_shift($args);

        $join = new Join($this->connection);
        $join->handle_where($args);

        $this->joins[] = 'INNER JOIN ' . $this->connection->quote_table($this->prefix.$table) . ' AS ' .
                         $this->connection->quote_table($table) . ' ON ' . $join->where_clause;

        return $this;
    }


    public function where()
    {
        $this->where_obj->handle_where(func_get_args());
        return $this;
    }

    public function or_where()
    {
        $this->where_obj->handle_or_where(func_get_args());
        return $this;
    }

    public function order($col)
    {
        $this->ordered[] = $this->connection->quote_column($col);
        return $this;
    }
    
    public function order_desc($col)
    {
        $this->ordered[] = $this->connection->quote_column($col) . ' DESC';;
        return $this;
    }

    public function skip($count)
    {
        $this->offset = $count;
        return $this;
    }

    public function take($count)
    {
        $this->limit = $count;
        return $this;
    }

    public function get($cols='*')
    {
        // Format column sql
        if(!is_array($cols))
        {
            $cols = explode(',', $cols);
        }
        
        $colsql = array();
        foreach($cols as $key => $value)
        {
            if($value == '*')
            {
                $colsql[] = '*';
            }
            else
            {
                if(is_int($key))
                {
                    $colsql[] = $this->connection->quote_column($value);
                }
                else
                {
                    $colsql[] = $this->->connection->quote_column($key) . ' AS ' . $this->connection->quote_column($value);
                }
            }
        }

        // Build select statement
        $sql = 'SELECT ' . implode(',', $colsql) . ' FROM ' . $this->connection->quote_table($this->prefix . $this->table) . ' AS ' . $this->connection->quote_table($this->table); 

        if(count($this->joins) > 0)
        {
            $sql .= ' '. implode(' ', $this->joins);
        }

        if($this->where_obj->where_clause)
        {
            $sql .= ' ' . $this->where_obj->where_clause;
        }

        if(count($this->ordered))
        {
            $sql .= ' ' . implode(',', $this->ordered);
        }

        if($this->limit || $this->offset)
        {
            $sql .=  ' ' . $this->connection->format_limit($this->limit, $this->offset);
        }

        return $this->driver->query($sql);
    }

    public function first($cols='*')
    {
        $query = $this->get($cols);
        $result = $query->next();
        $query = null;

        return $result;
    }

    public function increment($col, $count=1)
    {
        $col = $this->connection->quote_column($col);
        $count = (int)$count;

        $sql = 'UPDATE '. $this->prefix . $this->table . "SET $col=$col+$count";
        if($this->where_obj->where_clause)
        {
            $sql .= ' ' . $this->where_obj->where_clause;
        }
        return $this->driver->exec($sql);
    }
}
