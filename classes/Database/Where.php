<?php

// File:        Where.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     Handle where clauses

namespace MrBavii\SiteHelper\Database;

class Where
{
    protected $connection = null;
    public $where_clause = "";

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function where()
    {
        $this->handle_where(func_get_args());
        return $this;
    }
    
    public function or_where()
    {
        $sql = $this->handle_or_where(func_get_args());
        return $this;
    }

    public function handle_where($args)
    {
        if(strlen($this->where_clause) > 0)
        {
            $this->where_clause .= ' AND ';
        }

        $this->where_clause .= $this->format_where($args);
    }

    public function handle_or_where($args)
    {
        if(strlen($this->where_clause) > 0)
        {
            $this->where_clause .= ' OR ';
        }

        $this->where_clause .= $this->format_where($args);
    }

    protected function format_where($args)
    {
        switch(count($args))
        {
            case 1:
                return $this->format_where_closure($args[0]);

            case 2:
                return $this->format_where_null($args[0], $args[1]);

            case 3:
                return $this->format_where_comp($args[0], $args[1], $args[2]);

            default:
                throw new Exception('Invalid arguments');
        }
    }

    protected function format_where_closure($callback)
    {
        $tmp = new Where($this->connection);
        call_user_func($callback, $tmp);
        return '(' . $tmp->where_clause . ')';
    }

    protected function format_where_null($col, $comp)
    {
        switch($comp)
        {
            case 'null':
            case 'NULL':
                return $this->connection->format_isnull($col);

            case 'notnull':
            case 'NOTNULL':
                return $this->connection->format_isnotnull($col);

            default;
                throw new Exception('Invalid arguments');
        }
    }

    protected function format_where_comp($col, $comp, $value)
    {
        switch($comp)
        {
            case '=':
                return $this->connection->quote_column($col) . ' = ' . $this->connection->quote_value($value);

            case '!=':
                return $this->connection->quote_column($col) . ' != ' . $this->connection->quote_value($value);

            case '<':
                return $this->connection->quote_column($col) . ' < ' . $this->connection->quote_value($value);

            case '>':
                return $this->connection->quote_column($col) . ' > ' . $this->connection->quote_value($value);

            case '<=':
                return $this->connection->quote_column($col) . ' <= ' . $this->connection->quote_value($value);

            case '>=':
                return $this->connection->quote_column($col) . ' >= ' . $this->connection->quote_value($value);

            case 'like':
            case 'LIKE':
                return $this->connection->format_islike($col, $value);
            
            case 'notlike':
            case 'NOTLIKE':
                return $this->connection->format_isnotlike($col, $value);

            default:
                throw new Exception('Invalid arguments');
        }
    }
}

