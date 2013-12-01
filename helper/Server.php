<?php

namespace mrbavii\helper;

/**
 * A class to deal with information about the server and actions by the server.
 */
class Server
{
    /**
     * Determine the file type of a file
     */
    public static function getFileType($filename, $use_extension=TRUE)
    {

        // First try any configured file extensions
        if($use_extension && ($types = Config::get('server.filetypes')) !== null)
        {
            foreach($types as $ending => $type)
            {
                $len = strlen($ending);
                if(substr($filename, -$len) == $ending)
                {
                    return $type;
                }
            }
        }

        // Determine from file contents
        $magic = Config::get('server.magicfile');
        if($magic !== null)
        {
            $finfo = new \finfo(FILEINFO_SYMLINK, $magic);
        }
        else
        {
            $finfo = new \finfo(FILEINFO_SYMLINK);
        }

        if($finfo)
        {
            $type = $finfo->file($filename, FILEINFO_MIME_TYPE);
            if($type !== FALSE)
            {
                return $type;
            }
        }

        // Return default type if unable to determine file type
        return Config::get('server.filetype', 'application/octet-stream');
    }

    /**
     * Map a URI path to a file based on an alias
     */
    public static function getAlias($uripath)
    {
        $aliases = Config::get('server.alias');
        if($aliases === null)
        {
            return FALSE;
        }

        foreach($aliases as $prefix => $path)
        {
            if(Util::startsWith($uripath, $prefix))
            {
                return $path . substr($uripath, strlen($prefix));
            }
        }

        // Not found
        return FALSE;
    }
}

