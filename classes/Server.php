<?php

namespace mrbavii\sitehelper;

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

    /**
     * Download a file to the browser, setting appropriate headers.
     * This function does not return unless it throws an exception.
     *
     * @param filename The filename of the file to send.
     * @param download Send the file as a download if TRUE.
     */
    public static function sendFile($filename, $download=FALSE)
    {
        if(!is_file($filename) || !is_readable($filename))
            throw new Exception('The file does not exist or is not readable: $filename');

        // TODO: Clear buffer disable compression, maybe disable time limit
        while(@ob_end_clean());
        @error_reporting(0);

        // Session::flush();

        // Handle if-modified-since and set Last-Modified
        $content_mtime = filemtime($filename);
        if(Response::ifModifiedSince($content_mtime) === FALSE)
            exit();
        
        // Content name and length
        $content_name = basename($filename);
        $content_length = filesize($filename);

        // Content type
        if($download)
        {
            // Always octet-stream
            $content_type = 'application/octet-stream';
        }
        else
        {
            $content_type = static::getFileType($filename);
        }

        // Set headers 
        header('Content-Type: '. $content_type);
        header('Content-Length: '. strval($content_length));
        if($download)
        {
            header("Content-Disposition: attachment; filename=\"$content_name\"");
        }

        // Send file through only if needed
        if(Request::getMethod() == 'head')
        {
            exit();
        }

        // Support for ranges
        if(isset($_SERVER['HTTP_RANGE']))
        {
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $offset = abs(intval($matches[1]));
            if($offset >= $content_length)
            {
                $offset = 0;
            }

            if(!empty($matches[2]))
            {
                $length = abs(intval($matches[2])) - $offset + 1;

                if($offset + $length > $content_length)
                {
                    $length = $content_length - $offset;
                }
            }
            else
            {
                $length = $content_length - $offset;
            }
        }
        else
        {
            $offset = 0;
            $length = $content_length;
        }

        // Send the file now
        if(static::readFile($filename, $offset, $length, $content_length) === FALSE)
            throw new Exception('An error occured while sending the file.');

        exit();
    }

    /**
     * Read a file and send it through
     * @todo: Workaround for files > 2-4GB
     *
     * @param filename The name of the file
     * @return bytes sent on success, FALSE on error.
     */
    public static function readFile($filename, $offset=0, $length, $filesize)
    {
        // If a handler is configured to do this, then use the handler
        // It is up to the handler to set the Range headers as supported
        $handler = Config::get('server.sendfile');
        if($handler !== null)
        {
            $handler($filename, $offset, $length, $filesize);
            return;
        }

        // We do our own slow internal read file
        header('Accept-Ranges: bytes');
        if($offset != 0)
        {
            header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) - 1 . '/' . $filesize);
            Response::status(206, 'Partial Content');
        }

        $chunksize = 1024 * 1024;

        $handle = fopen($filename, 'rb');
        if($handle === FALSE)
            return FALSE;

        if(fseek($handle, $offset) != 0)
        {
            fclose($handle);
            return FALSE;
        }

        $size = filesize($filename);
        if($length === null)
            $length = $size - $offset;

        $sent = 0;
        while(!feof($handle) && connection_status() == 0 && $sent < $length)
        {
            if($chunksize > $length - $sent)
                $chunksize = $length - $sent;

            $chunk = fread($handle, $chunksize);
            print($chunk);
            $sent += strlen($chunk);
        }
        fclose($handle);

        return $sent;
    }
}

