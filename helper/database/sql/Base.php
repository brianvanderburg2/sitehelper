<?php

// File:        Base.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for SQL helpers 

namespace mrbavii\helper\database\sql;

abstract class Base
{
    abstract public function sql($grammar);
}

