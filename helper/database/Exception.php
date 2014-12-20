<?php

// File:        Exception.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Exception class for the database routines

namespace mrbavii\helper\database;

class Exception extends \Exception
{
    public function __construct($e)
    {
        if($e instanceof Exception)
        {
            $this->state = $e->state;
            $this->code = $e->code;
            $this->message = $e->message;
        }
        else if(strstr($e->getMessage(), 'SQLSTATE['))
        {
            preg_match("/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/", $e->getMessage(), $matches);
            $this->state = $matches[1];
            $this->code = $matches[2];
            $this->message = $matches[3];
        }
        else
        {
            $this->state = '';
            $this->code = '';
            $this->message = '';
        }
    }
    
    public function errorState()
    {
        return $this->state;
    }

    public function errorCode()
    {
        return $this->code;
    }

    public function errorMessage()
    {
        return $this->message;
    }

}

