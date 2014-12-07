<?php

// File:        listdir.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Show a directory listing.  

// I attempt to include some features from Apache mod_autoindex, such as icons
// and the ability to include a header or footer file.  Unlike Apache, the
// header/footer is a specific file, not per-directory, and is only included
// when building a raw listing.  These files are included as PHP, so can
// contain PHP code.

// Route configuration:
//
// icons      - array of 'content-type' => 'icon url', can also include '#DIRECTORY#', '#PARENT#', '#UNKNOWN#'
// raw        - if TRUE, only send the table, not the opening and closing HTML
// header     - Header file to include if sending raw
// footer     - Footer file to include if sending raw
// stylesheet - Send a stylesheet link
// showhidden - Show hidden files
// precision  - Precision for size display, default is 2
// date       - Date format, default: 'Y-M-d H:i:s'
//
// Also depends on Server configuration values such as filetypes and aliases

namespace mrbavii\apps\listing;
use mrbavii\helper\Server;
use mrbavii\helper\Template;
use mrbavii\helper\Config;

class App
{
    protected static $showhidden;
    protected static $icons;
    protected static $precision;
    protected static $date;

    protected static function app_config()
    {
        return array(
            'template.path' => array(array('mrbavii.listing.', __DIR__ . '/templates'))
        );
    }

    public static function execute($user_config)
    {
        Config::set(static::app_config(), $user_config);

        $config = Config::get('app', array());

        // Get our info
        static::$icons = isset($config['icons']) ? $config['icons'] : array();
        static::$showhidden = isset($config['showhidden']) ? $config['showhidden'] : FALSE;
        static::$precision = isset($config['precision']) ? $config['precision'] : 2;
        static::$date = isset($config['date']) ? $config['date'] : 'Y-M-d h:i:s';

        // Basic setup
        list($uripath) = explode('?', $_SERVER['REQUEST_URI']);
        $rawuripath = rawurldecode($uripath);

        $path = Server::getAlias($rawuripath);
        if($path === FALSE)
        {
            $path = $_SERVER['DOCUMENT_ROOT'] . $rawuripath;
        }

        if($_SERVER['PHP_SELF'] == $rawuripath || !is_dir($path))
        {
            die('Unable to call ' . $rawuripath . ' directly.');
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
                        'n' => $entry,
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

        // Build the parameters
        $params = array();

        $params['path'] = $rawuripath;

        foreach(array('n', 's', 'm', 't') as $item)
        {
            $link = $uripath . '?sort=' . $item;
            if($sortkey == $item && !isset($_GET['order']))
            {
                $link .= '&order=desc';
            }

            $params['links'][$item] = $link;
        }

        $params['contents'] = array();

        $params['contents'][] = static::content('../', 'Parent Directory', FALSE, FALSE, '#PARENT#');

        // Folders
        foreach($folderlist as $folder)
        {
            $params['contents'][] = static::content(rawurlencode($folder['n']) . '/' , $folder['n'] . '/', $folder['m'], $folder['s'], $folder['t']);
        }

        // Files
        foreach($filelist as $file)
        {
            $params['contents'][] = static::content(rawurlencode($file['n']), $file['n'], $file['m'], $file['s'], $file['t']);
        }

        Template::send('mrbavii.listing.main', $params);
        exit(0);
    }

    protected static function content($link, $name, $modified, $size, $type)
    {
        $result = array();

        $result['l'] = $link;
        $result['n'] = $name;
        $result['m'] = ($modified === FALSE) ? $modified : date(static::$date, $modified);
        $result['s'] = ($size === FALSE) ? $size : static::formatBytes($size);
        $result['t'] = ($type == '#PARENT#' || $type == '#DIRECTORY#') ? 'Directory' : $type;


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

        $result['i'] = $icon;
        return $result;
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

