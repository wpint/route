<?php 
namespace Wpint\Route\Concretes;

use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Route\Traits\RouteCollectorTrait;
use Wpint\Contracts\Hook\HookContract;
use \Illuminate\Support\Str;
use Wpint\Route\Route;

class AjaxRoute extends Route implements HookContract
{
    use RouteCollectorTrait;

    /**
     * route's action
     *
     * @var [type]
     */
    private $action;
 
    /**
     * Register the ajax route
     *
     * @return void
     */
    public function register()
    {
        add_action( "wp_ajax_{$this->getAction()}", [$this, 'wpResgisterAjaxRoute'] );
    }

    /**
     * Route scope
     *
     * @return RouteScopeEnum
     */
    public static function scope() : RouteScopeEnum
    {
        return RouteScopeEnum::AJAX;
    }

    /**
     * register & call the controller's  ajax callback
     *
     * @return void
     */
    public function wpResgisterAjaxRoute()
    {
        $controller = app($this->controller)->middleware($this->middleware);
        return $controller->callAction($this->function, [1]);
    }

    /**
     * set ajax's action 
     *
     * @param string $action
     * @return self
     */
    public function action(string $action) : self
    { 
        $this->action = $action;
        return $this;
    }

    /**
     * get ajax's action
     *
     * @return string
     */
    public function getAction() : string
    {
        if(!$this->action && !$this->name){
            $this->action = Str::random(10);
        }
        return $this->action ?? $this->name;
    }

}