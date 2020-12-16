<?php


namespace Presentation;


use Presentation\DefaultApi\DefaultApiModule;
use Presentation\IdentityApi\IdentityApiModule;
use Spatial\Core\Interfaces\IApplicationBuilder;
use Spatial\Core\Interfaces\IWebHostEnvironment;
use Spatial\Router\Interfaces\IRouteBuilder;
use Spatial\Core\Attributes\ApiModule;

#[ApiModule(
    imports: [
    IdentityApiModule::class,
    DefaultApiModule::class,
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
     * @param IApplicationBuilder $app
     * @param IWebHostEnvironment|null $env
     */
    public function configure(IApplicationBuilder $app, ?IWebHostEnvironment $env = null): void
    {
//        if ($env->isDevelopment()) {
//            $app->useDeveloperExceptionPage();
//        }

//        $endpoints = new RouteBuilder();


        $app->useHttpsRedirection();

        $app->useRouting();

        $app->useAuthorization();

        $app->useEndpoints(
            fn(IRouteBuilder $endpoints) => [
                $endpoints->mapControllers()
            ]

        );
    }
}