<?php


namespace Infrastructure\Services;


use Spatial\Router\Interfaces\CanActivate;

class AuthUser implements CanActivate
{

    public function canActivate(string $url): bool
    {
        return true;
        // TODO: Implement canActivate() method.
    }
}