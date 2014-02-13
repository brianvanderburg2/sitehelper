<?php

namespace mrbavii\helper\captcha;

abstract class Driver
{
    protected $settings = null;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    abstract public function connect();
    abstract public function disconnect();
    abstract public function connected();

    abstract public function create();
    abstract public function display($data);
    abstract public function verify($data, $uservalue);
}

