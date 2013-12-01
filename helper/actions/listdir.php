<?php

// File:        listdir.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Show a directory listing.  

// I attempt to include some features from Apache mod_autoindex, such as icons
// and the ability to include a header or footer file.  Unlike Apache, the
// header/footer is a specific file, not per-directory, and is only included
// when building a raw listing.  These files are included as PHP, so can
// contain PHP code.

// Configuration under action.mrbavii.helper:
//
// listdir.icons      - array of 'content-type' => 'icon url', can also include '#DIRECTORY#', '#PARENT#', '#UNKNOWN#'
// listdir.raw        - if TRUE, only send the table, not the opening and closing HTML
// listdir.header     - Header file to include if sending raw
// listdir.footer     - Footer file to include if sending raw
// listdir.stylesheet - Send a stylesheet link
// listdir.showhidden - Show hidden files
// listdir.precision  - Precision for size display, default is 2
// listdir.date       - Date format, default: 'Y-M-d H:i:s'
//
// Also depends on Server configuration values such as filetypes and aliases

namespace mrbavii\helper\actions\listdir;
use mrbavii\helper\Config;
use mrbavii\helper\Server;

class DirListing
{
    protected static $showhidden;
    protected static $icons;
    protected static $header;
    protected static $footer;
    protected static $raw;
    protected static $stylesheet;
    protected static $precision;
    protected static $date;

    public static function show($uripath=null)
    {
        // Get our info
        static::$icons = Config::get('action.mrbavii.helper.listdir.icons', array());
        static::$raw = Config::get('action.mrbavii.helper.listdir.raw', FALSE);
        static::$header = Config::get('action.mrbavii.helper.listdir.header', FALSE);
        static::$footer = Config::get('action.mrbavii.helper.listdir.footer', FALSE);
        static::$stylesheet = Config::get('action.mrbavii.helper.listdir.stylesheet', FALSE);
        static::$showhidden = Config::get('action.mrbavii.helper.listdir.showhidden', FALSE);
        static::$precision = Config::get('action.mrbavii.helper.listdir.precision', 2);
        static::$date = Config::get('action.mrbavii.helper.listdir.date', 'Y-M-d H:i:s');

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
                if($entry == '.' || $entry == '..')
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

            closedir($handle);
        }

        // Sort the arrays
        $sortkey = isset($_GET['sort']) && in_array($_GET['sort'], array('n', 's', 'm', 't')) ? $_GET['sort'] : 'n';
        $sortorder = isset($_GET['order']) ? SORT_DESC : SORT_ASC;
        $sortflags = ($sortkey == 's' || $sortkey == 'm') ? SORT_NUMERIC : SORT_STRING;

        $sortkeys = array();
        foreach($folderlist as $folder)
        {
            $sortkeys[] = $folder[$sortkey];
        }
        array_multisort($sortkeys, $sortorder, $sortflags, $folderlist);
        
        $sortkeys = array();
        foreach($filelist as $file)
        {
            $sortkeys[] = $file[$sortkey];
        }
        array_multisort($sortkeys, $sortorder, $sortflags, $filelist);

        // Send out the stuff
        static::sendHeader($uripath, $path);

        $headlinks = array();
        foreach(array('n', 's', 'm', 't') as $item)
        {
            $link = $uripath . '?sort=' . $item;
            if($sortkey == $item && !isset($_GET['order']))
            {
                $link .= '&order=desc';
            }

            $headlinks[$item] = htmlspecialchars($link);
        }

        print <<<OPENTABLE
<table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th class="i">&nbsp;</th>
            <th class="n"><a href="{$headlinks['n']}">Name</a></th>
            <th class="m"><a href="{$headlinks['m']}">Modified</a></th>
            <th class="s"><a href="{$headlinks['s']}">Size</a></th>
            <th class="t"><a href="{$headlinks['t']}">Type</a></th>
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
        $modified = ($modified !== FALSE) ? date(static::$date, $modified) : '&nbsp;';
        $size = ($size !== FALSE) ? static::formatBytes($size) : '&nbsp;';

        list($btype) = explode('/', $type);
        $btype .= '/';

        $icons = static::$icons;
        if(isset($icons[$type]))
        {
            $icon = $icons[$type];
        }
        elseif(isset($icons[$btype]))
        {
            $icon = $icons[$btype];
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
            $desc = 'Directory';
        }
        else
        {
            $desc = htmlspecialchars($type);
        }

        $even = $even ? 'e' : 'o';

        print <<<ENTRY
    <tr class="$even">
        <td class="i">$icon</td>
        <td class="n"><a href="$link">$name</a></td>
        <td class="m">$modified</td>
        <td class="s">$size</td>
        <td class="t">$desc</td>
    </tr>
ENTRY;
    }

    protected static function formatBytes($size)
    {
        static $sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'KB', 'B');

        $total = count($sizes);
        while($total-- && $size > 1024)
        {
            $size /= 1024;
        }

        return sprintf('%.' . static::$precision . 'f', $size) . $sizes[$total];
    }
}

DirListing::show();
