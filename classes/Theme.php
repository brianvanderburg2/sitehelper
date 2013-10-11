<?php

namespace mrbavii\sitehelper;

class Theme
{
    public static function get()
    {
        // List all registered themes
        $themes = Config::get('theme', array());

        return array_keys($themes);
    }

    public static function send($theme, $group, $prefix='')
    {
        $theme = ($theme == null) ? 'default' : $theme;
        $group = ($group == null) ? 'default' : $group;

        $tests = array(
            array($theme, $group),
            array($theme, 'default'),
            array('default', $group),
            array('default', 'default')
        );

        foreach($tests as $t)
        {
            $stylesheets = Config::get('theme.' . $t[0] . '.' . $t[1]);
            if($stylesheets !== null)
            {
                break;
            }
        }

        if($stylesheets === null)
        {
            // TODO: should probably error out here
            $stylesheets = array();
        }
        elseif(!is_array($stylesheets))
        {
            $stylesheets = array($stylesheets);
        }

        header('Content-Type: text/css');
        foreach($stylesheets as $stylesheet)
        {
            if(Url::isAbsolute($stylesheet))
            {
                echo "@import url(\"$stylesheet\");\n";
            }
            else
            {
                echo "@import url(\"$prefix$stylesheet\");\n";
            }
        }

        exit();
    }
}

