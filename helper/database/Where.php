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

            case 4:
                return $this->formatWhereBetween($args[0], $args[1], $args[2], $args[3]);

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

    protected function formatWhereComp($col, $comp, $right)
    {
        $grammar = $this->grammar;
        $vcol = $grammar->quoteColumn($col);
        $vright = Sql::value($right);
        switch($comp)
        {
            case '=':
                return $vcol . ' = ' . $vright->sql($grammar);

            case '!=':
            case '<>':
                return $vcol . ' <> ' . $vright->sql($grammar);

            case '<':
                return $vcol . ' < ' . $vright->sql($grammar);

            case '>':
                return $vcol . ' > ' . $vright->sql($grammar);

            case '<=':
                return $vcol . ' <= ' . $vright->sql($grammar);

            case '>=':
                return $vcol . ' >= ' . $vright->sql($grammar);

            case 'like':
            case 'LIKE':
                return $grammar->formatLike($col, $right);
            
            case 'notlike':
            case 'NOTLIKE':
                return $grammar->formatNotLike($col, $right);

            default:
                throw new Exception('Invalid arguments');
        }
    }

    protected function formatWhereBetween($col, $comp, $min, $max)
    {
        $grammar = $this->grammar;

        $vcol = $grammar->quoteColumn($col);
        $vmin = Sql::value($min);
        $vmax = Sql::value($max);
        switch($comp)
        {
            case 'between':
            case 'BETWEEN':
                return $vcol . ' BETWEEN ' . $vmin->sql($grammar) . ' AND ' . $vmax->sql($grammar);

            case 'notbetween':
            case 'NOTBETWEEN':
                return $vcol . ' NOT BETWEEN ' . $vmin->sql($grammar) . ' AND ' . $vmax->sql($grammar);

            default:
                throw new Exception('Invalid arguments');
        }
    }
}

