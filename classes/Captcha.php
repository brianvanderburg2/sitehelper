<?php

// File:        Captcha.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Captcha generation and verification

namespace mrbavii\sitehelper;

class Captcha
{
    protected static $instance = null;
    protected static $drivers = array(
        'basic' => '\mrbavii\sitehelper\captcha\BasicDriver.php'
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

        // Get driver and settings
        $driver = Config::first('captcha.driver', 'basic');
        $settings = Config::first('captcha.' . $driver, array());
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
            throw new Exception('No captcha driver');
        }

        if(isset(static::$drivers[$driver]))
        {
            $factory = static::$drivers[$driver];

            if($factory instanceof \Closure)
            {
                return $factory($settings);
            }
            else
            {
                return new $factory($settings);
            }
        }
        else
        {
            throw new Exception('Unknown captcha driver: ' . $driver);
        }
    }

    // Quick access methods
    public static function create()
    {
        return static::instance()->create();
    }

    public static function display($data)
    {
        return static::instance()->display($data);
    }

    public static function verify($data, $uservalue)
    {
        return static::instance()->verify($data, $uservalue);
    }

}

