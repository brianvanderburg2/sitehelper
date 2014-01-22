<?php

// File:        Value.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Handle values in SQL

namespace mrbavii\helper\database\sql;

class Value extends Base
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function sql($grammar)
    {
        if($this->value instanceof Base)
        {
            return $this->value->sql($grammar);
        }
        else
        {
            return $grammar->quoteValue($this->value);
        }
    }
}

