<?php

// File:        Driver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Base class for connecting to a cache

namespace mrbavii\helper\cache;

/**
 * Base class for a Cache driver
 */
abstract class Driver
{
    public function __construct($settings)
    {
        $this->connect($settings);
    }

    /**
     * Create a new connection to the driver.
     *
     * \param $settings The settings to pass to the driver.
     */
    abstract public function connect($settings);

    /**
     * Set a cached value.
     *
     * \param $name The name of the value.
     * \param $value The value to set.
     * \param $lifetime The lifetime of the named value in seconds.
     */
    abstract public function set($name, $value, $lifetime=null);

    /**
     * Get a cached value
     *
     * \param $name The name of the value
     * \param $def The default value.
     * \return The cached value, or the default value if the named value does not exist in the cache.
     */
    abstract public function get($name, $def=null);

    /**
     * Remove a cached value.
     *
     * \param $name The name of the value to remove from the cache.
     */
    abstract public function remove($name);

    /**
     * Check cache connection.
     *
     * \return TRUE if the driver is connected to the cached, otherwise FALSE.
     */
    abstract public function connected();

    /**
     * Remember a value if it does not already exist.
     *
     * \param $name The name of the value to set or get.
     * \param $value The value to set if the named value does not already exist.
     * \param $lifetime The lifetime of the value in seconds.
     * \return the cached value if it is already set, otherwise the passed in value.
     */
    public function remember($name, $value, $lifetime=null)
    {
        $result = $this->get($name);
        if($result === null)
        {
            $this->set($name, $value, $lifetime);
            return $value;
        }
        else
        {
            return $result;
        }
    }

    /**
     * Check a cached value
     *
     * \param $name The name of the value to check
     * \return TRUE if the named value exists, otherwise FALSE.
     */
    public function has($name)
    {
        return ($this->get($name) !== null) ? TRUE : FALSE;
    }
}

