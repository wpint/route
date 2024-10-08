<?php 
namespace Wpint\Route;

use Illuminate\Support\Collection;
use Wpint\Contracts\Hook\HookContract;
use Wpint\Route\Contracts\RouteContract;
use Wpint\Route\Enums\RouteScopeEnum;

abstract class Route
{

    /**
     * route's name
     *
     * @var string
     */
    protected $name = '';

    /**
     * route's path
     *
     * @var string
     */
    protected string $path;

    /**
     * route's scope
     *
     * @var RouteScopeEnum
     */
    protected static RouteScopeEnum $scope;

    /**
     * route's controller
     *
     * @var string
     */
    protected string $controller;

    /**
     * route's controller's method
     *
     * @var string
     */
    protected string $function;

    /**
     * route's middlewares
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * add the route to the collector
     *
     * @param RouteContract $route
     * @return void
     */
    public static function addToCollector(HookContract $route)
    {
        $collector = RouteCollector::getInstance();
        $collector->addRoute($route);
        return $route;
    }
 
    /**
     * set route's controller class and metod
     *
     * @param array $controller
     * @return self
     */
    public function controller(array $controller) : self
    {
        $this->controller = $controller[0];
        $this->function = $controller[1];
        return $this;
    }

    /**
     * set route's middlewares
     *
     * @param [type] ...$middleware
     * @return self
     */
    public function middleware(...$middleware) : self|null
    {
        if(!$middleware) return null;
        $this->middleware = array_merge($this->middleware, $middleware);
        return $this;
    }

    /**
     * set route scope
     *
     * @return void
     */
    public abstract static function scope();

    /**
     * set route's path
     *
     * @param string $path
     * @return self
     */
    public function path(string $path) : self
    {
        $this->path = trim($path, '/');
        return $this;
    }

    /**
     * set route's name
     *
     * @param string $name
     * @return self
     */
    public function name(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get all scoped routes 
     *
     * @return Collection
     */
    public static function getRoutes() : Collection
    {
        $calledClass = get_called_class();
        if("Wpint\Route\Route" === $calledClass)
        {
            return RouteCollector::getRoutes();
        }
        
        return RouteCollector::getRoutes()->filter(fn($route) => $route instanceof $calledClass);

    }

    /**
     * Get route by URI
     *
     * @param string $uri
     * @return Route|null
     */
    public static function getRouteByUri(string $uri) : Route | null
    {
        return self::getRoutes()->filter(fn($route) => isset($route->path) && $route->path == $uri)->first();
    }

}