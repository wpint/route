<?php 
namespace Wpint\Route;

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
    protected RouteScopeEnum $scope;

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
    public static function scope()
    {
        // this method has not been implemented by this class
    }

    /**
     * set route's path
     *
     * @param string $path
     * @return self
     */
    public function path(string $path) : self
    {
        $this->path = $path;
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


}