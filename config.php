<?php

declare(strict_types=1);

use Spatial\Router\Route;
use Spatial\Router\RouterModule;

/**
 * Configuration class for the API.
 * Toggle the $enableProdMode to show or hide CQured's error reporter.
 * Set Hearders & Corss Origin Settings.
 * Api's Router file path can be changed here too.
 */
class Config
{
    // General & PHP Doctrine Config
    public $enableProdMode = false;

    public $appName = 'Spatial';
    public $offline = [
        'value' => false,
        'offlineMessage' => 'This site is down for maintenance.<br />Please check back again soon.',
        'displayOfflineMessage' => '1',
        'offlineImage' => '',
    ];


    public $cliConfig = [
        'class' => 'playDB', //entityManager will be $em{{dbClassName}}
        'namespace' => 'resource', //namspace & file Location will be Infrastructure/{{dbNamespace}}
    ];


    function __construct()
    {
        if (!$this->offline['value']) {

            $this->routeModule = new RouterModule();
        }
    }


    private function _appRoute()
    {
        $route = new Route();
        $route->mapRoute(
            "DefaultAPI", // name
            "default/{controller}/{id:int}", //routeTemplate
            new class ()
            {
                public $id = 0;
            } //defaults
        );

        // initialize the RouterModule to set routes
        $this->routeModule->routeConfig($route)
            ->allowedMethods('GET, POST, PUT, DELETE')
            ->enableCache($this->enableProdMode)
            ->authGuard() // takes in list objects for authorization with interface CanActivate
            ->defaultContentType('application/json')
            ->controllerNamespaceMap('Presentation\\{name}\\Controllers\\'); // {name} refers to the route name
    }

    public function render()
    {
        $this->_appRoute();
        $this->routeModule->render();
    }


    // Routing

}
