<?php
declare(strict_types=1);
namespace Infrastructure\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SubModuleMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo "SubModuleMiddleware is logging the request.\n";
        $response = $handler->handle($request);
        echo "SubModuleMiddleware is logging the response.\n";
        return $response;
    }
}