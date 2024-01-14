<?php 
namespace Wpint\Route\Concretes;

use Wpint\Route\Traits\RouteCollectorTrait;
use Wpint\Route\Enums\RouteScopeEnum;
use Wpint\Contracts\Hook\HookContract;
use Wpint\Route\Route;

class AdminRoute extends Route implements HookContract
{
    use RouteCollectorTrait;

    /**
     * Page's title 
     *
     * @var string
     */
    private string $pageTitle = 'Page Title';

    /**
     * Admin's menu title
     *
     * @var string
     */
    private string $menuTitle = 'Menu Title';

    /**
     * Admin page access capability
     *
     * @var string
     */
    private string $capability = 'manage_options';
    
    /**
     * Admin page menu's icon 
     *
     * @var string
     */
    private string $icon = '';

    /**
     * $position
     *
     * @var integer|null
     */
    private int|null $position = null;

    /**
     * The parent of this page
     *
     * @var AdminRoute|null
     */
    private AdminRoute|null $parent = null;


    /**
     * Register the page
     *
     * @return void
     */
    public function register()
    {
        add_action('admin_menu', [$this, 'wpRegisterAdminRoute']);
    }

    /**
     * Route scope
     *
     * @return RouteScopeEnum
     */
    public static function scope() : RouteScopeEnum
    {
        return RouteScopeEnum::ADMIN;
    }

    /**
     * set Parent of page
     *
     * @param AdminRoute $parent
     * @return void
     */
    public function parent(AdminRoute $parent) : self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * set capability of page
     *
     * @param string $capability
     * @return void
     */
    public function capability(string $capability) : self
    {
        $this->capability = $capability;
        return $this;
    }

    /**
     * set menuTitle of page
     *
     * @param string $menuTitle
     * @return void
     */
    public function menuTitle(string $menuTitle) : self
    {
        $this->menuTitle = $menuTitle;
        return $this;
    }

    /**
     * set pageTitle of page
     *
     * @param string $pageTitle
     * @return void
     */
    public function pageTitle(string $pageTitle) : self
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * set page icon
     *
     * @param string $icon
     * @return void
     */
    public function icon(string $icon) : self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * set position of page menu
     *
     * @param integer $position
     * @return self
     */
    public function position(int $position) : self
    {
        $this->position = $position;
        return $this;  
    }

    /**
     * execution of wp function: add_menu_page|add_submenu_page 
     *
     * @return void
     */
    public function wpRegisterAdminRoute()
    {
        if(!$this->parent){
            add_menu_page(
                __( $this->pageTitle, 'textdomain' ),
                __( $this->menuTitle, 'textdomain' ),
                $this->capability,
                $this->path,
                function() {
                    $controller = app($this->controller)->middleware($this->middleware);
                    return $controller->callAction(
                        $this->function, 
                        [1]
                    );
                },
                $this->icon,
                $this->position
            );
        }
        else
        {
            add_submenu_page(
                $this->parent->path, 
                __( $this->pageTitle, 'textdomain' ),
                __( $this->menuTitle, 'textdomain' ),
                $this->capability,
                $this->path, 
                function() {
                    $controller = app($this->controller)->middleware($this->middleware);
                    return $controller->callAction($this->function, ['test']);
                }, 
                $this->position
            );
        }
      
    }
    

}