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
    public bool $enableProdMode = false;

    public string $appName = 'Spatial';
    public array $offline = [
        'value' => false,
        'message' => 'This site is down for maintenance.<br />Please check back again soon.',
        'displayMessage' => '1',
        'image' => '',
    ];


    public array $cliConfig = [
        'class' => 'playDB', //entityManager will be $em{{dbClassName}}
        'namespace' => 'resource', //namespace & file Location will be Infrastructure/{{dbNamespace}}
    ];
    private RouterModule $routeModule;

    public function __construct()
    {
        if (!$this->offline['value']) {
            $this->routeModule = new RouterModule();
        }
    }

    /**
     * @throws ReflectionException
     */
    public function render(): void
    {
        $this->_appRoute();
        $this->routeModule->render();
    }

    /**
     *
     */
    private function _appRoute(): void
    {
        $route = new Route();
        $route->mapRoute(
            'DefaultAPI', // name
            'default/{controller}/{id:int}', //routeTemplate
            new class () {
                public int $id = 0;
                public string $content;

                public function __construct()
                {
                    $this->content = file_get_contents('php://input');
                }
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


    // Routing

}
