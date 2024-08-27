<?php 
namespace Wpint\Route\Concretes;

use Illuminate\Support\Str;
use Wpint\Contracts\Hook\HookContract;
use Wpint\Route\Enums\RouteHttpMethodEnum;
use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Route\Route;
use Wpint\Route\Traits\RouteCollectorTrait;

class WebRoute extends Route implements HookContract
{
    use RouteCollectorTrait;

    /**
     * route's method
     *
     * @var [type]
     */
    protected $method = RouteHttpMethodEnum::GET;

    /**
     * route's parameters
     *
     * @var array
     */
    protected $params = [];


    /**
     * register the route
     *
     * @return void
     */
    public function register()
    {

        add_filter('template_include', [$this, 'wpResgisterWebRoute']);
    }


    /**
     * set route's http method
     *
     * @param [type] $method
     * @return self
     */
    public function method(RouteHttpMethodEnum $method = RouteHttpMethodEnum::GET) : self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Route scope
     *
     * @return RouteScopeEnum
     */
    public static function scope() : RouteScopeEnum
    {
        return RouteScopeEnum::WEB;
    }


    /**
     * set route's path
     *
     * @param string $path
     * @return self
     */
    public function path(string $uri) : self
    {   
        $uri = trim($uri, '/');
        $baseUri = $uri;
        $uri = preg_replace('/\{[^\}]+\}/', '([^/]+)', $uri); // Replace {param} with a regex pattern
        $uri = '/^' . str_replace('/', '\/', $uri) . '$/'; // Convert to a regex pattern        
        $this->path = $uri;

        // extract params 
        preg_match($this->path, $baseUri, $params);
        if($params)
        {
            array_shift($params);
            $params = array_map(fn ($p) => trim($p, '/\{[^\}]+\}/'), $params);
            $this->params($params);
        }
        
        return $this;
    }

    /**
     * set route's parameters
     *
     * @param array $params
     * @return self
     */
    public function params(array $params)
    {
        $this->params = $params;
        return $this;
    }
    
    public static function getRequestedRoute() : Route | null
    {
        $requestUri = trim(request()->getRequestUri(), '/');
        $requestMethod = Str::lower(request()->method());

        return self::getRoutes()->first(function($route) use ($requestUri, $requestMethod) {
            if(
                $requestMethod === Str::lower($route->method->name) &&
                preg_match($route->path, $requestUri, $matches)
            ){
                array_shift($matches);
                $route->params(array_combine(array_values($route->params), array_values($matches)));
                return $route;
            };
        });
    }

    /**
     * Registeration of the web route
     *
     * @return void
     */
    public function wpResgisterWebRoute($template)
    {
        global $post;

        // route resolve
        $route = self::getRequestedRoute();
        if(
            $route && 
            strtolower(request()->method()) === strtolower($route->method->name)
        ){
            $resolved = app($route->controller);
            return $resolved->callAction($route->function, $route->params);
        }

        if (!$post) {

            return $template;
        }
        
        return $template;
    }



}