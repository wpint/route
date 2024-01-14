<?php 
namespace Abrz\WPDF\Services\Route\Concretes;

use Abrz\WPDF\Services\Route\Contracts\RouteContract;
use Abrz\WPDF\Services\Route\Enums\RouteScopeEnum;
use Abrz\WPDF\Services\Route\Route;
use Abrz\WPDF\Services\Route\Traits\RouteCollectorTrait;

class WebRoute extends Route implements RouteContract
{
    use RouteCollectorTrait;

    /**
     * register the route
     *
     * @return void
     */
    public function register()
    {
        $this->wpResgisterWebRoute();
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
     * Registeration of the web route
     *
     * @return void
     */
    private function wpResgisterWebRoute()
    {
        return true;
    }

}