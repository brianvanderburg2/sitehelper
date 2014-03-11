<?php

// File:        Join.php
// Author:      Brian Allen Vanderburg Ii
// Purpose:     Handle join clauses

namespace mrbavii\helper\database;

class Join
{
    protected $grammar = null;
    public $sql = "";

    public function __construct($grammar)
    {
        $this->grammar = $grammar;
    }

    public function on()
    {
        $this->handleOn(func_get_args());
        return $this;
    }
    
    public function orOn()
    {
        $this->handleOrOn(func_get_args());
        return $this;
    }

    public function handleOn($args)
    {
        if(strlen($this->sql) > 0)
        {
            $this->sql .= ' AND ';
        }

        $this->sql .= $this->formatOn($args);
    }

    public function handleOrOn($args)
    {
        if(strlen($this->sql) > 0)
        {
            $this->sql .= ' OR ';
        }

        $this->sql .= $this->formatOn($args);
    }

    protected function formatOn($args)
    {
        switch(count($args))
        {
            case 1:
                return $this->formatOnClosure($args[0]);

            case 2:
                return $this->formatOnComp($args[0], '=', $args[1]);

            case 3:
                return $this->formatOnComp($args[0], $args[1], $args[2]);

            default:
                throw new Exception('Invalid arguments');
        }
    }

    protected function formatOnClosure($callback)
    {
        $tmp = new Join($this->grammar);
        call_user_func($callback, $tmp);
        return '(' . $tmp->sql . ')';
    }

    protected function formatOnComp($col1, $comp, $col2)
    {
        $vcol1 = $this->grammar->quoteColumn($col1);
        $vcol2 = $this->grammar->quoteColumn($col2);
        switch($comp)
        {
            case '=':
                return $vcol1 . ' = ' . $vcol2;

            case '!=':
            case '<>':
                return $vcol1 . ' <> ' . $vcol2;

            case '<':
                return $vcol1 . ' < ' . $vcol2;

            case '>':
                return $vcol1 . ' > ' . $vcol2;

            case '<=':
                return $vcol1 . ' <= ' . $vcol2;

            case '>=':
                return $vcol1 . ' >= ' . $vcol2;

            default:
                throw new Exception('Invalid arguments');
        }
    }
}

