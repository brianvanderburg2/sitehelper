<?php

// File:        Driver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Define the session's driver interface

namespace mrbavii\helper\session;

class PhpDriver extends Driver
{
    protected $prefix = '';

    public function __construct($settings)
    {
        if(isset($settings['prefix']))
        {
            $this->prefix = $settings['prefix'];
        }

        parent::__construct($settings);
    }

    public function connect()
    {
        // Try to avoid calling session_start if started automatically
        if(!isset($_SESSION) || session_id() == '')
        {
            session_start();
            $_SESSION[$this->prefix . '_MRBAVII_SITEHELPER_'] = TRUE;
        }
    }

    public function disconnect()
    {
    }

    public function destroy()
    {
        // This code is borrowed from PHP online docs
        $_SESSION = array();
        
        if(ini_get('session.use_cookies'))
        {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }

        session_destroy();
    }

    public function setValue($name, $value)
    {
        $_SESSION[$this->prefix . $name] = $value;
    }

    public function getValue($name, $defval=null)
    {
        if(isset($_SESSION[$this->prefix . $name]))
        {
            return $_SESSION[$this->prefix . $name];
        }
        else
        {
            return $defval;
        }
    }

    public function clearValue($name)
    {
        unset($_SESSION[$this->prefix . $name]);
    }

    public function checkValue($name)
    {
        return isset($_SESSION[$this->prefix . $name]);
    }

}

