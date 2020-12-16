<?php


namespace Presentation\IdentityApi;

use Presentation\IdentityApi\Controllers\AuthorizationController;
use Presentation\IdentityApi\Controllers\UserController;
use Spatial\Core\Attributes\ApiModule;

#[ApiModule(
    imports: [
],
    declarations: [
    AuthorizationController::class,
    UserController::class
],
    providers: [
],
    bootstrap: [AuthorizationController::class]
)]
class IdentityApiModule
{

}