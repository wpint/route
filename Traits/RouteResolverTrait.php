<?php 
namespace Wpint\Route\Traits;

use WPINT\Framework\Include\Middleware\Handler;
use Wpint\Support\CallbackResolver;

trait RouteResolverTrait
{
    
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
        $request = app('request');
        $next = function($request) use ($callback, $params) {
            return  app()->call($callback, $params);
        };
        // Run all the middlewares
        Handler::evaluate($this->middleware, $next);
        return $next($request);
    }

}
