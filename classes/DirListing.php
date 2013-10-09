<?php

// File:        DirListing.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Show a directory listing.  

// I attempt to include some features from Apache mod_autoindex, such as icons
// and the ability to include a header or footer file.  Unlike Apache, the
// header/footer is a specific file, not per-directory, and is only included
// when building a raw listing.  These files are included as PHP, so can
// contain PHP code.

// Configuration:
//
// listing.icons      - array of 'content-type' => 'icon url', can also include '#DIRECTORY#', '#PARENT#', '#UNKNOWN#'
// listing.raw        - if TRUE, only send the table, not the opening and closing HTML
// listing.header     - Header file to include if sending raw
// listing.footer     - Footer file to include if sending raw
// listing.stylesheet - Send a stylesheet link
// listing.showhidden - Show hidden files
//
// Also depends on Server configuration values such as filetypes and aliases

namespace mrbavii\sitehelper;


class DirListing
{
    protected static $showhidden;
    protected static $icons;
    protected static $header;
    protected static $footer;
    protected static $raw;
    protected static $stylesheet;

    public static function show($uripath=null)
    {
        // Get our info
        static::$icons = Config::get('listing.icons', array());
        static::$raw = Config::get('listing.raw', FALSE);
        static::$header = Config::get('listing.header', FALSE);
        static::$footer = Config::get('listing.footer', FALSE);
        static::$stylesheet = Config::get('listing.stylesheet', FALSE);
        static::$showhidden = Config::get('listing.showhidden', FALSE);

        // Basic setup
        if($uripath === null)
        {
            list($uripath) = explode('?', $_SERVER['REQUEST_URI']);
            $uripath = rawurldecode($uripath);
        }

        $path = Server::getAlias($uripath);
        if($path === FALSE)
        {
            $path = $_SERVER['DOCUMENT_ROOT'] . $uripath;
        }

        if($_SERVER['PHP_SELF'] == $uripath || !is_dir($path))
        {
            Event::fire('404');
            die('Unable to call ' . $uripath . ' directly.');
        }

        // Get the files and folders
        $filelist = array();
        $folderlist = array();

        if($handle = opendir($path))
        {
            while(($entry = readdir($handle)) !== FALSE)
            {
                // Don't show '.', '..', and maybe not hidden items
                if($entry == '.' || $item == '..')
                {
                    continue;
                }
                   
                if(!static::$showhidden && (substr($entry, 0, 1) == '.' || substr($entry, -1) == '~'))
                {
                    continue;
                }

                // Information about file or directory
                $realpath = $path . '/' . $entry;
                if(is_dir($realpath))
                {
                    $folderlist[] = array(
                        'n' => $entry . '/',
                        's' => FALSE,
                        'm' => filemtime($realpath),
                        't' => '#DIRECTORY#'
                    );
                }
                elseif(is_file($realpath))
                {
                    $filelist[] = array(
                        'n' => $entry,
                        's' => filesize($realpath),
                        'm' => filemtime($realpath),
                        't' => Server::getFileType($realpath)
                    );
                }
            }

            fclose($handle);
        }

        // Send out the stuff
        static::sendHeader($uripath, $path);

        print <<<OPENTABLE
<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th class="i">&nbsp;</th>
            <th class="n">Name</th>
            <th class="m">Modified</th>
            <th class="s">Size</th>
            <th class="t">Type</th>
        </tr>
    </thead>
    <tbody>
OPENTABLE;

        $even = FALSE;
        // Parent link
        if(ltrim($uripath, '/') != '')
        {
            static::sendRow('../', 'Parent Directory', FALSE, FALSE, '#PARENT#', $even);
            $even = !$even;
        }

        // Folders
        foreach($folderlist as $folder)
        {
            static::sendRow($folder['n'], $folder['n'], $folder['m'], $folder['s'], $folder['t'], $even);
            $even = !$even;
        }

        // Files
        foreach($filelist as $file)
        {
            static::sendRow($file['n'], $file['n'], $file['m'], $file['s'], $file['t'], $even);
            $even = !$even;
        }

        print <<<CLOSETABLE
    </tbody>
</table>
CLOSETABLE;

        static::sendFooter($uripath, $path);

    }

    protected static function sendHeader($uripath, $path)
    {
        if(static::$raw == FALSE)
        {
            $stylesheet = '';
            if(static::$stylesheet !== FALSE)
            {
                $stylesheet = '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars(static::$stylesheet) . '" />';
            }
            $uripath = htmlspecialchars($uripath);

            print <<<HEADING
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Listing for $uripath</title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        $stylesheet
    </head>
    <body>
        <h2>Listing of <span>$uripath</span></h2>
HEADING;
        }
        else
        {
            include static::$header;
        }
    }

    protected static function sendFooter($uripath, $path)
    {
        if(static::$raw == FALSE)
        {
            print <<<FOOTING
        <div class="footer">Generated with SiteHelper DirListing</div>
    </body>
</html>
FOOTING;
        }
        else
        {
            include static::$footer;
        }
    }

    protected static function sendRow($link, $name, $modified, $size, $type, $even)
    {
        $link = htmlspecialchars($link);
        $name = htmlspecialchars($name);
        $modified = ($modified !== FALSE) ? date('Y-M-d H:m:s', $modified) : '&nbsp;';
        $size = ($size !== FALSE) ? static::formatBytes($size, 2) : '&nbsp;';

        $icons = static::$icons;
        if(isset($icons[$type]))
        {
            $icon = $icons[$type];
        }
        elseif(isset($icons['#UNKNOWN#']))
        {
            $icon = $icons['#UNKNOWN#'];
        }
        else
        {
            $icon = FALSE;
        }

        if($icon !== FALSE)
        {
            $icon = '<a href="' . $link . '"><img src="' . htmlspecialchars($icon) . '" alt="" /></a>';
        }
        else
        {
            $icon = '&nbsp;';
        }

        if($type == '#DIRECTORY#' || $type == '#PARENT#')
        {
            $type = 'Directory';
        }

        $type = htmlspecialchars($type);

        $even = $even ? 'e' : 'o';

        print <<<ENTRY
    <tr class="$even">
        <td class="i">$icon</td>
        <td class="n"><a href="$link">$name</a></td>
        <td class="m">$modified</td>
        <td class="s">$size</td>
        <td class="t">$type</td>
    </tr>
ENTRY;
    }

    protected static function formatBytes($size, $precision=0)
    {
        static $sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'KB', 'B');

        $total = count($sizes);
        while($total-- && $size > 1024)
        {
            $size /= 1024;
        }

        return sprintf('%.' . $precision . 'f', $size) . $sizes[$total];
    }
}

