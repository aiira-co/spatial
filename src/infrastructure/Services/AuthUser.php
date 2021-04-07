<?php


namespace Infrastructure\Services;


use Psr\Http\Message\ServerRequestInterface;
use Spatial\Router\Interfaces\IsAuthorizeInterface;

class AuthUser implements IsAuthorizeInterface
{

    public function isAuthorized(ServerRequestInterface $request): bool
    {
        return true;
        // TODO: Implement canActivate() method.
    }
}