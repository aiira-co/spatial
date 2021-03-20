<?php


namespace Infrastructure\Services;


use Spatial\Router\Interfaces\IsAuthorizeInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthUser implements CanActivate
{

    public function isAuthorized(ServerRequestInterface $request): bool
    {
        return true;
        // TODO: Implement canActivate() method.
    }
}