<?php


namespace Presentation;


use Presentation\WebApi\WebApiModule;
use Presentation\IdentityApi\IdentityApiModule;
use Spatial\Core\Interfaces\ApplicationBuilderInterface;
use Spatial\Core\Interfaces\WebHostEnvironmentInterface;
use Spatial\Router\Interfaces\RouteBuilderInterface;
use Spatial\Core\Attributes\ApiModule;

#[ApiModule(
    imports: [
    IdentityApiModule::class,
    WebApiModule::class,
],
    declarations: [],
    providers: [
],
    bootstrap: []
)]
class AppModule
{
    /**
     * Method is called for app configuration
     * configure routing here
     * @param ApplicationBuilderInterface $app
     * @param WebHostEnvironmentInterface|null $env
     */
    public function configure(ApplicationBuilderInterface $app, ?WebHostEnvironmentInterface $env = null): void
    {
//        if ($env->isDevelopment()) {
//            $app->useDeveloperExceptionPage();
//        }

//        $endpoints = new RouteBuilder();


        $app->useHttpsRedirection();

        $app->useRouting();

        $app->useAuthorization();

        $app->useEndpoints(
            fn(RouteBuilderInterface $endpoints) => [
                $endpoints->mapControllers()
            ]

        );
    }
}