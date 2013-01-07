<?php

namespace mrbavii\sitehelper;

/**
 * A class for sending responses back to the browser.
 */
class Response extends Browser
{
    /**
     * Disable caching of pages.
     */
    public static function noCache()
    {
        header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
        /**
         * @todo send 'Expires' header as well.
         */
    }

    /**
     * Enable caching of pages.
     *
     * @param delta The maximum number of seconds the page should be cached.
     * @param private If the cache should be considered private.
     */
    public static function cache($delta=0, $private=TRUE)
    {
        $visibility = $private ? 'private' : 'public';
        $duration = ($delta > 0) ? ", max-age=$delta" : '';

        header("Cache-Control: {$visibility}{$duration}");
        /**
         * @todo send 'Expires' header as well.
         */
    }
    
    /**
     * Enable shared caching of pages instead of private caching.
     *
     * @param delta The maximum number of seconds the page should be cached.
     */
    public static function share($delta=0)
    {
        static::cache($delta, FALSE);
    }

    /**
     * Redirect to another location.
     *
     * @param url An absolute URL either with or without the domain and
     *  protocol.  If specified as '/path/to/file', the host, protocol,
     *  and port number will automatically be added.  If no colon is present
     *  and it does not begin with a '/', then the redirect based on the
     *  script entry point.
     * @return This method does not return.
     */
    public static function redirect($url, $code=303)
    {
        // No colon means using current domain
        $pos = strpos($url, ':')
        if($pos === FALSE)
        {
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
            {
                $proto = 'https://';
            }
            else
            {
                $proto = 'http://';
            }

            if(isset($_SERVER['HTTP_HOST']))
            {
                // HTTP_HOST includes port if it was used
                $server = $_SERVER['HTTP_HOST'];
            }
            else
            {
                // SERVER_NAME does not include port even if used
                $server = $_SERVER['SERVER_NAME'];
                if(isset($_SERVER['SERVER_PORT']))
                {
                    $server = $server. ':' . $_SERVER['SERVER_PORT'];
                }
            }

            // If url does not begin with '/' like '/path/to/file' use entry
            if($url[0] != '/')
            {

                $url = $_SERVER['SCRIPT_NAME'] . '/' . $url;
            }

            $url = $proto . $server . $url;
        }

        $this->noCache();
        header("Status: $code");
        header('Location: '.$url, TRUE, $code);
        exit();
    }

    /**
     * Set the status header.
     *
     * @param status The status to set.
     * @param desc The description of the status.
     */
    public static function status($status, $desc=null)
    {
        $str = "$status";
        if($desc !== null)
            $str .= " $desc";

        if(substr(php_sapi_name(), 0, 3) == 'cgi')
        {
            header("Status: $str");
        }
        else
        {
            header("{$_SERVER['SERVER_PROTOCOL']} $str");
        }
    }

    /**
     * This will set the last modified timestamp and, if If-Modified-Since is set
     * set the correct status header if needed.
     *
     * @param timestamp The timestamp being checked.
     * @return TRUE if modified, otherwise FALSE.
     */
    public static function ifModifiedSince($timestamp)
    {
        $now = time();
        if($timestamp > $now)
            $timestamp = $now;

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', $timestamp));
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
        {
            $if_modified_since = strtotime(preg_replace('#;.*$#', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']));
            if($if_modified_since >= $timestamp)
            {
                $this->Status(304, 'Not Modified');
                return FALSE;
            }
        }
            
        return TRUE;
    }

    /**
     * Force access through a specified wrapper URL.  If not accessed via the
     * wrapper URL then redirect.
     *
     * @param wrapper The name of the wrapper URL.
     */
    public static function forceWrapper($wrapper)
    {
        $script = $_SERVER['SCRIPT_NAME'];
        if($script != $wrapper)
        {
            $url = $wrapper;

            // Pass PATH_INFO
            if(isset($_SERVER['PATH_INFO']))
                $url .= $_SERVER['PATH_INFO'];

            // Pass query string
            if(count($_GET) > 0)
                $url .= '?' . http_build_query($_GET, '', '&');

            $this->Redirect($url);
        }
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

        // Handle if-modified-since and set Last-Modified
        $content_mtime = filemtime($filename);
        if(static::ifModifiedSince($content_mtime) === FALSE)
            exit();
        
        // Content name and length
        $content_name = basename($filename);
        $content_length = filesize($filename);

        // Content type
        $content_type = null;
        if($download)
        {
            // Always octet-stream
            $content_type = 'application/octet-stream';
        }
        
        if($content_type === null)
        {
            // Check file ending first
            foreach(static::$types as $ending => $type)
            {
            }
            $parts = explode('.', $content_name);
            if(count($parts) >= 2)
            {
                $ext = array_pop($parts);
                if(isset(static::$types[$ext]))
                    $content_type = static::$types[$ext];
            }
        }
        
        if($content_type === null)
        {
            // Use mime-info
            $finfo = new \finfo(FILEINFO_MIME);

            $content_type = $finfo->file($filename, FILEINFO_MIME_TYPE);
            unset($finfo);
        }

        if($content_type === null)
        {
            $content_type = 'application/octet-stream';
        }

        // Set headers 
        header('Content-Type: '.$content_type);
        header('Content-Length: '.strval($content_length));
        if($download)
            header("Content-Disposition: attachment; filename=\"$content_name\"");

        // Send file through if needed
        if($_SERVER['REQUEST_METHOD'] == 'HEAD')
            exit();

        // TODO: support for ranges (Resume support)
        if(static::readFile($filename) === FALSE)
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
    public static function readFile($filename, $offset=0, $length=null)
    {
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

