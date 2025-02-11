<?php 
namespace Wpint\Route;

use Illuminate\Support\Collection;
use Wpint\Contracts\Hook\HookContract;
use WPINT\Framework\Include\Middleware\Handler;
use Wpint\Route\Contracts\RouteContract;
use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Support\CallbackResolver;

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
     * route's callback
     *
     * @var string
     */
    protected $callback;

    // /**
    //  * route's controller's method
    //  *
    //  * @var string
    //  */
    // protected string $function;

    /**
     * route's middlewares
     *
     * @var array
     */
    protected $middleware = [];


    public function __construct(string $name, array|callable $callback)
    {
        $this->name($name);
        $this->callback($callback);
    }


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
    public function callback($callback) : self
    {
        $this->callback = $callback;
        return $this;
    }


    /**
     * alias of the callback method 
     *
     * @param array $controller
     * @return self
     */
    public function controller($callback) : self
    {
        $this->callback($callback);
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
    public static function getRouteByUri(string $uri): Route|null
    {
        return self::getRoutes()->filter(fn($route) => isset($route->path) && $route->path == $uri)->first();
    }

    /**
     * Get route by name
     *
     * @param string $name
     * @return Route|null
     */
    public static function getByName(string $name): Route|null
    {
        return self::getRoutes()->filter(fn($route) => isset($route->name) && $route->name === $name)->first();
    }

    /**
     * Run proper resolved callback 
     *
     * @param CallbackResolver $resolver
     * @return void
     */
    public function resolve(CallbackResolver $resolver)
    {
        $callback = $resolver->getCallback();
        $params = $resolver->getParams();
        if(is_array($callback) && isset($callback['class'])) {
            $resolved = app($callback['class']);
            return $resolved->callAction($callback['method'], $this->middleware, $params);
        }
        
        $next = function($request) use ($callback, $params) {
            return  app()->call($callback, $params);
        };
        // Run all the middlewares
        return Handler::evaluate($this->middleware, $next);
    }

}