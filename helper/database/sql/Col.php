<?php

// File:        Col.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Handle columns in SQL 

namespace mrbavii\helper\database\sql;

class Col extends Base
{
    protected $col;

    public function __construct($col)
    {
        $this->col = $col;
    }

    public function sql($grammar)
    {
        return $grammar->quoteColumn($this->col);
    }
}

