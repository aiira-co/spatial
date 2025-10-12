<?php

declare(strict_types=1);

namespace Infrastructure\Middlewares;

use Infrastructure\JWT\Token;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ClaimsMiddleware implements MiddlewareInterface
{
    public function __construct(private Token $token) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($this->_processAuthHeader($request));
        return $response;
    }


    private function _processAuthHeader(ServerRequestInterface $request):ServerRequestInterface
    {
        
        $header = $request->getHeaderLine('Authorization');
        if ($header === '')
            return $request;

        $jwt = $this->getBearerToken($header);
        if ($jwt === null)
            return $request;

         $parsed = $this->token->getParseToken($jwt);
        if (!$parsed) 
            return $request;


         $claims = [
            'userId' => $parsed->claims()->get('userId'),
            'appClaim' => $parsed->claims()->get('appClaim'),
        ];

        // Attach to request attributes
        return $request->withAttribute('claims', (object)$claims);

        
    }


    private function getBearerToken(?string $header): ?string
    {
        return preg_match('/Bearer\s+(\S+)/', (string)$header, $matches) ? $matches[1] : null;
    }
}
