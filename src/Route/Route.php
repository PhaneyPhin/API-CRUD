<?php

namespace Phaney\ApiCrud\Route;

use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route as BaseRoute;

class Route extends BaseRoute
{

    protected static $prefixRouteName = '';

    public function prefixName(string $name)
    {
        self::$prefixRouteName = $name;
    }

    public static function crud(string $uri, string $class) : RouteRegistrar
    {
        return BaseRoute::prefix($uri)->group(function () use ($class) {
            if (! self::$prefixRouteName) {
                $classes = explode('\\', $class);
                $className = $classes[count($classes) - 1];
                self::$prefixRouteName = strtolower(substr($className, 0, strlen($className) - strlen('Controller')));
            }

            self::$prefixRouteName .= '.';

            $class::setupRoute(self::$prefixRouteName);
        });
    }
}