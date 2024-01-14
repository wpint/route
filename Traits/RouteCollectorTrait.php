<?php 
namespace Wpint\Route\Traits;

trait RouteCollectorTrait
{
    /**
     * fix Scope and add it the to collector
     */
    public function __construct()
    {
        $this->addToCollector($this);
    }

}