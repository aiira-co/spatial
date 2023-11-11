<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Commands;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

use function json_encode;

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
        $data = $request->deleteUser();
        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
