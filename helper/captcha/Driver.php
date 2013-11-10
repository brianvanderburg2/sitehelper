<?php

namespace mrbavii\helper\captcha;

abstract class Driver
{
    public function __construct($settings)
    {
        $this->connect($settings);
    }

    abstract public function connect($settings);

    abstract public function create();
    abstract public function display($data);
    abstract public function verify($data, $uservalue);
}
