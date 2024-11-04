<?php 
namespace Wpint\Route\Concretes;

use Wpint\Route\Enums\RouteHttpMethodEnum;
use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Route\Traits\RouteCollectorTrait;
use Wpint\Contracts\Hook\HookContract;
use Wpint\Route\Route;
use WP_REST_Request;
use WP_Error;
use Closure;
use Wpint\Route\Traits\RouteResolverTrait;
use Wpint\Support\CallbackResolver;

class RestRoute extends Route implements HookContract
{
    use RouteCollectorTrait, RouteResolverTrait;

    /**
     * route's namespace
     *
     * @var string
     */
    private $namespace = '';

    /**
     * route's permission
     *
     * @var boolean
     */
    private $permission = true;

    /**
     * route's http method
     *
     * @var RouteHttpMethodEnum
     */
    protected RouteHttpMethodEnum $method = RouteHttpMethodEnum::GET;

    /**
     * Register the rest endpoint
     *
     * @return void
     */
    public function register()
    {   
        add_action('rest_api_init', [$this, 'wpRegisterRestEndpoint']);
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
        return RouteScopeEnum::REST;
    }

    /**
     * endpoint's namespace
     *
     * @param string $namespace
     * @return self
     */
    public function namespace(string $namespace) : self
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * route's permission
     *
     * @param Closure|string|array $permission
     * @return self
     */
    public function permission(Closure|string|array $permission) : self
    {
        if(!$permission) $this->permission = true;
        $this->permission = $permission;
        return $this;
    }

    /**
     * Execute the wp method: register_rest_route
     *
     * @return void
     */
    public function wpRegisterRestEndpoint()
    {

        register_rest_route(
            $this->namespace, 
            $this->path, 
            [
            'methods'  => $this->method->value,
            'callback'  => function($data)
            {
                
                $callback = CallbackResolver::export($this->callback, $data->get_params(), false);
                $resolved = app($callback['callback']['class']);
                $resolved->middleware($this->middleware);
                return $resolved->callAction($callback['callback']['method'], $callback['params']);
            },
            'permission_callback'   => function(WP_REST_Request $request)
                {
                    if(!$this->permission) return new WP_Error( 'rest_forbidden', esc_html__( "oops, You don't have access to this endpoint.", 'my-text-domain' ), array( 'status' => 401 ) );
                    return CallbackResolver::call($this->permission,  ['request' => $request], false);
                }
            ], 
            false
        );

    }
}