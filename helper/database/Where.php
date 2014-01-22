<?php

// File:        Where.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Handle where clauses

namespace mrbavii\helper\database;

class Where
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

            case 2:
                return $this->formatWhereNull($args[0], $args[1]);

            case 3:
                return $this->formatWhereComp($args[0], $args[1], $args[2]);

            default:
                throw new Exception('Invalid arguments');
        }
    }

    protected function formatWhereClosure($callback)
    {
        $tmp = new Where($this->grammar);
        call_user_func($callback, $tmp);
        return '(' . $tmp->sql . ')';
    }

    protected function formatWhereNull($col, $comp)
    {
        switch($comp)
        {
            case 'null':
            case 'NULL':
                return $this->grammar->formatNull($col);

            case 'notnull':
            case 'NOTNULL':
                return $this->grammar->formatNotNull($col);
            default;
                throw new Exception('Invalid arguments');
        }
    }

    protected function formatWhereComp($left, $comp, $right)
    {
        $vleft = new sql\Value($left);
        $vright = new sql\Value($right);
        switch($comp)
        {
            case '=':
                return $vleft->sql($this->grammar) . ' = ' . $vright->sql($this->grammar);

            case '!=':
                return $vleft->sql($this->grammar) . ' != ' . $vright->sql($this->grammar);

            case '<':
                return $vleft->sql($this->grammar) . ' < ' . $vright->sql($this->grammar);

            case '>':
                return $vleft->sql($this->grammar) . ' > ' . $vright->sql($this->grammar);

            case '<=':
                return $vleft->sql($this->grammar) . ' <= ' . $vright->sql($this->grammar);

            case '>=':
                return $vleft->sql($this->grammar) . ' >= ' . $vright->sql($this->grammar);

            case 'like':
            case 'LIKE':
                return $this->grammar->formatLike($left, $right);
            
            case 'notlike':
            case 'NOTLIKE':
                return $this->grammar->formatNotLike($left, $right);

            default:
                throw new Exception('Invalid arguments');
        }
    }
}

