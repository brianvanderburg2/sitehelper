<?php

// File:        Raw.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Handle raw SQL

namespace mrbavii\helper\database\sql;

class Raw extends Base
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function sql($grammar)
    {
        return $this->value;
    }
}

