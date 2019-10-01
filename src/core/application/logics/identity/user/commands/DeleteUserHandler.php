<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Commands;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class DeleteUserHandler extends RequestHandler
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
        $data = $request->getDeleteUser();
        $payload = \json_encode($data ?? []);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
