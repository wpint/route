<?php 
namespace Wpint\Route\Concretes;

use Illuminate\Support\Str;
use WP_Error;
use Wpint\Contracts\Hook\HookContract;
use Wpint\Route\Enums\RouteHttpMethodEnum;
use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Route\Route;
use Wpint\Route\Traits\RouteCollectorTrait;
use Wpint\Route\Traits\RouteResolverTrait;
use Wpint\Support\CallbackResolver;

use function PHPUnit\Framework\once;

class WebRoute extends Route implements HookContract
{
    use RouteCollectorTrait, RouteResolverTrait;

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


    private static $rendered = false;

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

    /**
     * Get request route from param
     *
     * @return void
     */
    public static function getRequestedRoute() : Route | null
    {
        $requestUri = trim(request()->getRequestUri(), '/');
        return self::getRoutes()->first(function($route) use ($requestUri) {
            if(
                preg_match($route->path, $requestUri, $matches) &&
                self::routeMethodGate($route->method->name) 
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

        // // route resolve
        
        $route = self::getRequestedRoute();
        if( $route && !self::$rendered ){
            $resolver = new CallbackResolver($route->callback, $route->params, false);
            self::$rendered = true;
            return $this->resolve($resolver);
        }

        if (!$post) {

            return $template;
        }
        
        return $template;
    }

    /**
     * Validate request method versus the given mehtod
     *
     * @param [type] $method
     * @return void
     */
    private static function routeMethodGate(string $method)
    {
        $requestMethod = Str::lower(request()->method());
        $method = Str::lower($method);
        
        if($method === RouteHttpMethodEnum::ANY->lower()) return true;    
    
        if(in_array($method, [
            RouteHttpMethodEnum::PUT->lower(),
            RouteHttpMethodEnum::PATCH->lower(),
            RouteHttpMethodEnum::DELETE->lower(),
        ])){
            if(request()->has(Str::of($method)->prepend('_')) && $requestMethod === RouteHttpMethodEnum::POST->lower()) 
                return true;
            else 
                throw new WP_Error('The route method is not supported');
        }
        
        if(in_array($method, [
            RouteHttpMethodEnum::GET->lower(),
            RouteHttpMethodEnum::POST->lower(),
        ])){
            if($requestMethod === $method) return true;
        }

        throw new WP_Error('The route method is not supported');
    }


}