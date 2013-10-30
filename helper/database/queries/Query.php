<?php

// File:        Query.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for a result from a query

namespace mrbavii\helper\database\queries;

abstract class Query
{
    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    abstract public function next();
}


