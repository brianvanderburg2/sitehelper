<?php

namespace mrbavii\helper;

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
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() - 86400) . ' GMT');
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
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $delta) . ' GMT');
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

            // If url does not begin with '/' like '/path/to/file' use REQUEST_URI 
            if($url[0] != '/')
            {
                // Add to REQUEST_URI
                $prefix = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

                if(substr($prefix, -1) != '/')
                {
                    // We are not a directory, so append url to the directory part only
                    $pos = strrpos($prefix, '/');
                    if($pos !== FALSE)
                    {
                        $prefix = substr($prefix, 0, $pos + 1);
                    }
                    else
                    {
                        $prefix = '/';
                    }
                }

                $url = $prefix . $url;
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
            // Pass PATH_INFO
            $url = $wrapper . Server::getPathInfo();

            // Pass query string
            if(count($_GET) > 0)
                $url .= '?' . http_build_query($_GET, '', '&');

            static::redirect($url);
        }
    }

}

