<?php

// File:        Expr.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Handle expressions in SQL

namespace mrbavii\helper\database\sql;
use mrbavii\helper\database\Sql;
use mrbavii\helper\database\Exception;

class Expr extends Base
{
    protected $value;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function sql($grammar)
    {
        $args = $this->args;

        if(count($args) == 2)
        {
            if($args[0] == '-' || $args[0] == '+')
            {
                return '(' . $args[0] . Sql::value($args[1])->sql($grammar) . ')';
            }
            else
            {
                throw new Exception('Invalid unary operator');
            }
        }
        else if(count($args) == 3)
        {
            if($args[1] == '+' || $args[1] == '-' || $args[1] == '*' || $args[1] == '/' || $args[1] == '%')
            {
                return '(' . Sql::value($args[0])->sql($grammar) . ' ' . $args[1] . ' ' . Sql::value($args[2])->sql($grammar) . ')';
            }
            else
            {
                throw new Exception('Invalid binary operator');
            }
        }
    }
}

