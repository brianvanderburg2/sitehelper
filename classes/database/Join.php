<?php

// File:        Join.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     Handle join clauses

namespace mrbavii\sitehelper\database;

class Join
{
    protected $grammar = null;
    public $sql = "";

    public function __construct($grammar)
    {
        $this->grammar = $grammar;
    }

    public function where()
    {
        $this->handleWhere(func_get_args());
        return $this;
    }
    
    public function orWhere()
    {
        $sql = $this->handleOrWhere(func_get_args());
        return $this;
    }

    public function handleWhere($args)
    {
        if(strlen($this->sql) > 0)
        {
            $this->sql .= ' AND ';
        }

        $this->sql .= $this->formatWhere($args);
    }

    public function handleOrWhere($args)
    {
        if(strlen($this->sql) > 0)
        {
            $this->sql .= ' OR ';
        }

        $this->sql .= $this->formatWhere($args);
    }

    protected function formatWhere($args)
    {
        switch(count($args))
        {
            case 1:
                return $this->formatWhereClosure($args[0]);

            case 3:
                return $this->formatWhereComp($args[0], $args[1], $args[2]);

            default:
                throw new Exception('Invalid arguments');
        }
    }

    protected function formatWhereClosure($callback)
    {
        $tmp = new Join($this->grammar);
        call_user_func($callback, $tmp);
        return '(' . $tmp->sql . ')';
    }

    protected function formatWhereComp($col1, $comp, $col2)
    {
        switch($comp)
        {
            case '=':
                return $this->grammar->quoteColumn($col1) . ' = ' . $this->grammar->quoteColumn($col2);

            case '!=':
                return $this->grammar->quoteColumn($col1) . ' != ' . $this->grammar->quoteColumn($col2);

            case '<':
                return $this->grammar->quoteColumn($col1) . ' < ' . $this->grammar->quoteColumn($col2);

            case '>':
                return $this->grammar->quoteColumn($col1) . ' > ' . $this->grammar->quoteColumn($col2);

            case '<=':
                return $this->grammar->quoteColumn($col1) . ' <= ' . $this->grammar->quoteColumn($col2);

            case '>=':
                return $this->grammar->quoteColumn($col1) . ' >= ' . $this->grammar->quoteColumn($col2);

            default:
                throw new Exception('Invalid arguments');
        }
    }
}

