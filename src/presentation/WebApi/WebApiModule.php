<?php


namespace Presentation\WebApi;

use Presentation\DefaultApi\Controllers\ValuesController;
use Spatial\Core\Attributes\ApiModule;

#[ApiModule(
    imports: [],
    declarations: [
        ValuesController::class
],
    providers: [
],
    bootstrap: [
        ValuesController::class
    ]
)]
class WebApiModule
{

}