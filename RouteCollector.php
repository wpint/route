<?php 
namespace Wpint\Route;

use Illuminate\Support\Collection;
use Wpint\Contracts\Hook\HookContract;

class RouteCollector
{

    /**
     * $instance
     *
     * @var [type]
     */
    private static $instance;

    /**
     * $routes
     *
     * @var Collection
     */
    private Collection $routes;

    /**
     * singleton
     *
     * @return void
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new self;
            self::$instance->routes = collect([]);
        }
        return self::$instance;
    }
    
    /**
     * make the routes
     *
     * @return void
     */
    public static function make()
    {
        self::getInstance()->getRoutes()
        ->each(function($route) {
            $route->register();
        });
    }
    
    /**
     * Add the route to collector
     *
     * @param HookContract $route
     * @return void
     */
    public function addRoute(HookContract $route) : void
    {
        $this->routes->add($route);
    }

    /**
     * Get all routes
     *
     * @return void
     */
    public static function getRoutes()
    {
        return self::$instance->routes;
    }


}