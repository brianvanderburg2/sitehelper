<?php

// File:        Param.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Handle PDO parameters in SQL

namespace mrbavii\helper\database\sql;

class Param extends Base
{
    protected $param;

    public function __construct($param)
    {
        $this->param = $param;
    }

    public function sql($grammar)
    {
        return ':' . $this->param;
    }
}

