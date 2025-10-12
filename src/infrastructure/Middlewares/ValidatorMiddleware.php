<?php
declare(strict_types=1);
namespace Infrastructure\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidatorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo "ValidatorMiddleware is validating the request.\n";
        $response = $handler->handle($request);
        echo "ValidatorMiddleware is validating the response.\n";
        return $response;
    }
}