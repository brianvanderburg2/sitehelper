<?php

namespace mrbavii\helper;

class Action
{
    public static function execute($action, $params=array())
    {
        // The action may contain parts that are not safe for the file
        // system, so we have to test each part when looking for the file.
        $paths = Config::get('path.actions');
        if(!$paths)
        {
            throw new Exception('No action path specified');
        }
        
        // Check if we started with '/', such as with path info
        if($action[0] == '/')
        {
            $action = substr($action, 1);
        }

        // Look through each part
        $parts = explode('/', $action);


        $subpath = '';
        while(count($parts) > 0)
        {
            $part = array_shift($parts);

            // Make sure part is safe as a file name
            if(!Security::checkPathComponent($part))
            {
                Event::fire('404');
                exit();
            }
            $subpath = $subpath . DIRECTORY_SEPARATOR . $part;

            // Look through each path
            foreach($paths as $path)
            {
                $file = $path . $subpath . '.php';
                if(is_file($file))
                {
                    Util::loadPhp($file, array('params' => $parts), TRUE);
                    exit();
                }
                else if(!is_dir($path. $subpath))
                {
                    break; // If the directory does not exist, no need to check for files under it
                }
            }
        }

        // Should not get here
        Event::fire('404');
        exit();
    }
}

