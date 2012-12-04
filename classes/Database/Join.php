<?php

// File:        Join.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     Handle join clauses

namespace MrBavii\SiteHelper\Database;

class Join
{
    protected $grammar = null;
    public $where_clause = "";

    public function __construct($grammar)
    {
        $this->grammar = $grammar;
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

            case 3:
                return $this->format_where_comp($args[0], $args[1], $args[2]);

            default:
                throw new Exception('Invalid arguments');
        }
    }

    protected function format_where_closure($callback)
    {
        $tmp = new Join($this->grammar);
        call_user_func($callback, $tmp);
        return '(' . $tmp->where_clause . ')';
    }

    protected function format_where_comp($col1, $comp, $col2)
    {
        switch($comp)
        {
            case '=':
                return $this->grammar->quote_column($col1) . ' = ' . $this->grammar->quote_column($col2);

            case '!=':
                return $this->grammar->quote_column($col1) . ' != ' . $this->grammar->quote_column($col2);

            case '<':
                return $this->grammar->quote_column($col1) . ' < ' . $this->grammar->quote_column($col2);

            case '>':
                return $this->grammar->quote_column($col1) . ' > ' . $this->grammar->quote_column($col2);

            case '<=':
                return $this->grammar->quote_column($col1) . ' <= ' . $this->grammar->quote_column($col2);

            case '>=':
                return $this->grammar->quote_column($col1) . ' >= ' . $this->grammar->quote_column($col2);

            default:
                throw new Exception('Invalid arguments');
        }
    }
}

