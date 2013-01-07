<?php

namespace mrbavii\sitehelper;

/**
 * The base class for request and response handling
 */
class Browser
{
    protected static $types = array();

    /**
     * Set the content type of a specific file ending.
     *
     * @param ending The file ending including the leading dot or an array of
     *  ending=>type pairs.
     * @param type The content type.
     */
    public static function registerType($ending, $type=null)
    {
        if(!is_array($ending))
            $ending = array($ending => $type);

        static::$types = array_merge(static::$types, $ending);
    }

    /**
     * Find the type based on the registered ending
     *
     * @param filename The filename to check for
     * @param normalize TRUE if case insensitive
     * @return the content type or FALSE
     */
     public static function findType($filename, $normalize=TRUE)
     {
        foreach(static::$types as $ending => $type)
        {
            $len = strlen($ending);
            if($len == 0)
                return $type;


        }
     }
}

