<?php

namespace mrbavii\helper;

class Router
{
    public static function dispatch($route=null)
    {
        // The route format is <group>/<route>
        // Group and route can contain '.' for namespacing
        // The route name may only contain letters and numbers
        // Each route is a separate file under the directory registered for
        // the given group.  The file ends in '.php'.
        // If the route file can not be found, it will fall back
        // to the mrbavii.helper/error.404

        // Determine the route
        if($route === null)
        {
            if(isset($_POST['route']))
            {
                $route = $_POST['route'];
            }
            else if(isset($_GET['route']))
            {
                $route = $_GET['route'];
            }
            else
            {
                $route = 'site/default';
            }
        }

        // Split into group/route parts
        $parts = explode('/', $route);
        if(count($parts) != 2)
        {
            $group = 'mrbavii.helper';
            $route = 'error.404';
        }
        else
        {
            $group = $parts[0];
            $route = $parts[1];
        }

        // Find the route
        $path = static::find($group, $route);
        if($path === FALSE)
        {
            $group = 'mrbavii.helper';
            $route = 'error.404';
            $path = static::find($group, $route);
        }

        if($path === FALSE)
        {
            throw Exception("No such route: ${group}/${route}");
        }

        // Determine the configuration for the route if any is specified
        $config = Config::get("route.${group}.${route}.config", array());

        // Source the route file
        $params = array(
            'config' => $config,
            'group' => $group,
            'route' => $route
        );
        Util::loadPhp($path, $params);
    }

    public static function find($group, $route)
    {
        // Check if characters are okay
        if(!preg_match("#^[a-zA-Z0-9\\.]*$#", $group))
        {
            return FALSE;
        }
        
        if(!preg_match("#^[a-zA-Z0-9\\.]*$#", $route))
        {
            return FALSE;
        }

        // Determine if route group exists
        $path = Config::get("route.${group}.path");
        if($path === null)
        {
            return FALSE;
        }

        # Filename for the route file
        $path = $path . '/' . str_replace('.', '/', $route) . '.php';
        if(!is_readable($path))
        {
            return FALSE;
        }

        return $path;
    }
}

