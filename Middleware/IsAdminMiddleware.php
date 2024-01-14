<?php 
namespace Wpint\Route\Middleware;

use Wpint\Contracts\Middleware\MiddlewareContract;
use Illuminate\Http\Request;
use Closure;

class IsAdminMiddleware implements MiddlewareContract
{

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        if(!is_super_admin()){
            wp_safe_redirect('/wp-admin');
            exit;
        }

        return $next($request);
    }
    
}