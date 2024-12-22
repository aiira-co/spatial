<?php


namespace Presentation\WebApi;

use Presentation\WebApi\Controllers\ValuesController;
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