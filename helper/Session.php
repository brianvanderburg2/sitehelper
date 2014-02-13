<?php

// File:        Session.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Session storage

namespace mrbavii\helper;

class Session
{
    protected static $instance = null;
    protected static $drivers = array(
        'php' => '\mrbavii\helper\session\PhpDriver'
    );

    public static function register($driver, $factory)
    {
        static::$drivers[$driver] = $factory;
    }

    public static function instance()
    {
        if(isset(static::$instance))
        {
            return static::$instance;
        }

        // Get driver name and settings
        $driver = Config::get('session.driver', 'php');
        $settings = Config::get('session.' . $driver, array());
        $settings['driver'] = $driver;

        return (static::$instance = static::connect($settings));
    }

    public static function connect($settings)
    {
        if(isset($settings['driver']))
        {
            $driver = $settings['driver'];
        }
        else
        {
            throw new Exception('No session driver');
        }

        if(isset(static::$drivers[$driver]))
        {
            $factory = static::$drivers[$driver];

            if($factory instanceof \Closure)
            {
                $instance = $factory($settings);
            }
            else
            {
                $instance = new $factory($settings);
            }

            $instance->connect();
            $instance->cleanupTimed();
            return $instance;
        }
        else
        {
            throw new Exception('Unknown session driver: ' . $driver);
        }
    }

    // Quick access methods
    public static function destroy()
    {
        return static::instance()->destroy();
    }

    public static function createValue($value=null)
    {
        return static::instance()->createValue($value);
    }

    public static function setValue($name, $value)
    {
        return static::instance()->setValue($name, $value);
    }

    public static function getValue($name, $defval=null)
    {
        return static::instance()->getValue($name, $defval);
    }

    public static function clearValue($name)
    {
        return static::instance()->clearValue($name);
    }

    public static function checkValue($name)
    {
        return static::instance()->checkValue($name);
    }

    public static function createTimed($value=null)
    {
        return static::instance()->createTimed($value);
    }

    public static function touchTimed($name)
    {
        return static::instance()->touchTimed($name);
    }

    public static function setTimed($name, $value)
    {
        return static::instance()->setTimed($name, $value);
    }

    public static function getTimed($name, $defval=null)
    {
        return static::instance()->getTimed($name, $defval);
    }

    public static function clearTimed($name)
    {
        return static::instance()->clearTimed($name);
    }

    public static function checkTimed($name)
    {
        return static::instance()->checkTimed($name);
    }

}


