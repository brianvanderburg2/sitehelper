<?php

// File:        Driver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Define the session's driver interface

namespace mrbavii\sitehelper\session;
use mrbavii\sitehelper\Config;
use mrbavii\sitehelper\Util;
use mrbavii\sitehelper\Exception;

abstract class Driver
{
    const timed_key = '_mrbavii_sitehelper_timed_';
    protected $timed_duration = 600;

    public function __construct($settings)
    {
        if(isset($settings['timed']['duration']))
        {
            $this->timed_duration = $settings['timed']['duration'];
        }

        $this->connect($settings);

        $this->cleanupTimed();
    }

    protected function cleanupTimed()
    {
        // Remove any timed variables that are expired
        $timestamp = time() - intval($this->timed_duration);
        $timed = $this->getValue(self::timed_key, array());

        $modified = FALSE;
        foreach(array_keys($timed) as $name)
        {
            if($timed[$name] < $timestamp)
            {
                $this->clearValue($name);
                unset($timed[$name]);
                $modified = TRUE;
            }
        }

        if($modified)
        {
            $this->setValue(self::timed_key, $timed);
        }
    }

    abstract public function connect($settings);
    abstract public function destroy();

    public function createValue($value=null)
    {
        for($count = 0; $count < 1000; $count++)
        {
            $name = str_replace('-', '', Util::guid());
            if(!$this->checkValue($name))
            {
                $this->setValue($name, $value);
                return $name;
            }
        }

        throw new Exception('Unable to create unique session variable.');
    }

    abstract public function setValue($name, $value);
    abstract public function getValue($name, $defval=null);
    abstract public function clearValue($name);
    abstract public function checkValue($name);

    public function createTimed($value=null)
    {
        $timestamp = time();
        $name = $this->createValue($value);

        $timed = $this->getValue(self::timed_key, array());
        $timed[$name] = $timestamp;
        $this->setValue(self::timed_key, $timed);

        return $name;
    }

    public function touchTimed($name)
    {
        $timed = $this->getValue(self::timed_key, array());
        if(isset($timed[$name]))
        {
            $timed[$name] = time();
            $this->setValue(self::timed_key, $timed);
        }
    }

    public function setTimed($name, $value)
    {
        if($this->checkTimed($name))
        {
            $this->setValue($name, $value);
        }
    }

    public function getTimed($name, $defval=null)
    {
        if($this->checkTimed($name))
        {
            return $this->getValue($name, $defval);
        }
        else
        {
            return $defval;
        }
    }

    public function clearTimed($name)
    {
        $timed = $this->getValue(self::timed_key, array());
        if(isset($timed[$name]))
        {
            $this->clearValue($name);

            unset($timed[$name]);
            $this->setValue(self::timed_key, $timed);
        }
    }

    public function checkTimed($name)
    {
        $timed = $this->getValue(self::timed_key, array());
        return isset($timed[$name]);
    }
}

