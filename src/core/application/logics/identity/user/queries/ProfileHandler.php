<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Queries;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class ProfileHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Return response
        // first get userinfo
        $data =  $request->getUser();
        $payload = \json_encode($data ?? []);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
