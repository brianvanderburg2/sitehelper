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
    protected $grammar = null;
    protected $connector = null;
    protected $prefix = '';
    protected $table = null;

    protected $joins = array();
    protected $where_obj = null;
    protected $ordered = array();

    protected $offset = null;
    protected $limit = null;

    public function __construct($connector, $table)
    {
        $this->connector = $connector;
        $this->grammar = $connector->grammar;
        $this->prefix = $connector->prefix;
        $this->table = $table;

        $this->where_obj = new Where($this->grammar);
    }
    
    public function join($table)
    {
        $args = func_get_args();
        array_shift($args);

        $join = new Join($this->grammar);
        $join->handle_where($args);

        $this->joins[] = 'INNER JOIN ' . $this->grammar->quote_table($this->prefix.$table) . ' AS ' .
                         $this->grammar->quote_table($table) . ' ON ' . $join->where_clause;

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
        $this->ordered[] = $this->grammar->quote_column($col);
        return $this;
    }
    
    public function order_desc($col)
    {
        $this->ordered[] = $this->grammar->quote_column($col) . ' DESC';;
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
        return $this->connector->query($this->get_sql($cols));
    }

    public function get_sql($cols='*')
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
                    $colsql[] = $this->grammar->quote_column($value);
                }
                else
                {
                    $colsql[] = $this->grammar->quote_column($key) . ' AS ' . $this->grammar->quote_column($value);
                }
            }
        }

        // Build select statement
        $sql = 'SELECT ' . implode(', ', $colsql) . ' FROM ' . $this->grammar->quote_table($this->prefix . $this->table) . ' AS ' . $this->grammar->quote_table($this->table); 

        if(count($this->joins) > 0)
        {
            $sql .= ' '. implode(' ', $this->joins);
        }

        if($this->where_obj->where_clause)
        {
            $sql .= ' WHERE ' . $this->where_obj->where_clause;
        }

        if(count($this->ordered))
        {
            $sql .= ' ORDER BY ' . implode(', ', $this->ordered);
        }

        if($this->limit || $this->offset)
        {
            $sql .=  ' ' . $this->grammar->format_limit($this->limit, $this->offset);
        }

        return $sql;
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
        return $this->connector->exec($this->increment_sql($col, $count));
    }

    public function increment_sql($col, $count=1, $action='+')
    {
        $col = $this->grammar->quote_column($col);
        $count = (int)$count;

        $sql = 'UPDATE '. $this->prefix . $this->table . "SET $col=$col$action$count";
        if($this->where_obj->where_clause)
        {
            $sql .= ' ' . $this->where_obj->where_clause;
        }

        return $sql;
    }

    public function decrement($col, $count=1)
    {
        return $this->connector->exec($this->decrement_sql($col, $count));
    }

    public function decrement_sql($col, $count=1)
    {
        return $this->increment_sql($col, $count, '-');
    }

    public function delete()
    {
        return $this->connector->exec($this->delete_sql());
    }

    public function delete_sql()
    {
        $sql = 'DELETE FROM ' . $this->prefix . $this->table;

        if(strlen($this->where_obj->where_clause) > 0)
        {
            $sql .= ' WHERE ' . $this->where_obj->where_clause;
        }
        else
        {
            $sql .= ' WHERE 1 = 1';
        }

        return $sql;
    }

    public function insert($cols)
    {
        return $this->connector->exec($this->insert_sql($cols));
    }

    public function insert_sql($cols)
    {
        // TODO:
    }

    public function update($cols)
    {
        return $this->connector->exec($this->update_sql($cols));
    }

    public function update_sql($cols)
    {
        // TODO:
    }

    public function create()
    {
        return $this->connector->exec($this->create_sql());
    }

    public function create_sql()
    {
        // Since this is database specific, we call the grammar to format this
    }
}
