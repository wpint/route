<?php 
namespace Wpint\Route;

use Wpint\Route\Concretes\AdminRoute;
use Wpint\Route\Concretes\AjaxRoute;
use Wpint\Route\Concretes\RestRoute;
use Wpint\Route\Concretes\WebRoute;
use Illuminate\Contracts\Events\Dispatcher;
use WPINT\Core\Foundation\Application;
use WPINT\Core\Foundation\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
   
    /**
     * Register All Routes service.
     *
     * @return void
     */
    public function register() : void 
    {
        // admin route
        $this->app->bind('route.admin', function(Application $app)
        {
            return new AdminRoute;
        });

        // ajax route
        $this->app->bind('route.ajax', function(Application $app)
        {
            return new AjaxRoute();
        });

        // rest route
        $this->app->bind('route.rest', function(Application $app)
        {
            return new RestRoute();
        });


        // rest route
        $this->app->bind('route.web', function(Application $app)
        {
            return new WebRoute();
        });
        
    }

    /**
     * Bootstrap route application service
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
    }

}