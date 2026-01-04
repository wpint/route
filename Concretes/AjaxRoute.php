<?php 
namespace Wpint\Route\Concretes;

use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Route\Traits\RouteCollectorTrait;
use Wpint\Contracts\Hook\HookContract;
use \Illuminate\Support\Str;
use Wpint\Route\Route;
use Wpint\Support\CallbackResolver;

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
     * route's security
     *
     * @var [type]
     */
    private $private = false;

    /**
     * Register the ajax route
     *
     * @return void
     */
    public function register()
    {
        if($this->private)
        {
            add_action( "wp_ajax_{$this->getAction()}", [$this, 'wpResgisterAjaxRoute'] );
        } else {
            add_action( "wp_ajax_{$this->getAction()}", [$this, 'wpResgisterAjaxRoute'] );
            add_action( "wp_ajax_nopriv_{$this->getAction()}", [$this, 'wpResgisterAjaxRoute'] );
        }

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
     * Route's security
     *
     * @return self
     */
    public function private() : self 
    {
        $this->private = true;
        return $this;
    }

    /**
     * register & call the controller's  ajax callback
     *
     * @return void
     */
    public function wpResgisterAjaxRoute()
    {
        // route resolve
        $resolver = new CallbackResolver($this->callback, [], false);             
        return $this->resolve($resolver);
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