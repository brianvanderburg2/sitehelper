<?php

// File:        FileDriver.php
// Author:      Brian Allen Vanderburg II
// Purpose:     A simple file cache driver

namespace mrbavii\helper\cache;

class FileDriver extends Driver
{
    protected $rootdir = null;

    public function connect($settings)
    {
        if(isset($settings['rootdir']))
        {
            $rootdir = $settings['rootdir'];
            if(is_writable($rootdir))
                $this->rootdir = $rootdir;
        }
    }

    public function set($name, $value, $lifetime=null);
    {
        // TODO:
    }

    public function get($name, $def=null)
    {
        // TODO:
    }

    public function remove($name)
    {
        $filename = $this->filename($name);
        if($filename !== null || is_file($filename))
        {
            unlink($filename);
        }
    }

    public function connected()
    {
        return $this->rootdir !== null;
    }

    protected function filename($name)
    {
        if($this->rootdir !== null)
        {
            return $this->rootdir . DIRECTORY_SEPARATOR . $name;
        }
        else
        {
            return null;
        }
    }

}

