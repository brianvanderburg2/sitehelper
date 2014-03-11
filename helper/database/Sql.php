<?php

// File:        Sql.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Some database SQL functions

namespace mrbavii\helper\database;

class Sql
{
    public static function raw($sql)
    {
        return new sql\Raw($sql);
    }
    
    public static function col($c)
    {
        return new sql\Col($c);
    }

    public static function expr()
    {
        return new sql\Expr(func_get_args());
    }

    public static function value($v)
    {
        return new sql\Value($v);
    }

    public static function param($p)
    {
        return new sql\Param($p);
    }
}

