<?php

namespace Core\Application\Logics\Identity\Authorization\Commands;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class SignOutHandler extends RequestHandler
{
    /**
     * @throws \JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Return response
        // check username & email again
        if (!$request->userExists()) {
            $data = [
                'success' => false,
                'message' => 'Activity was not found',
            ];
        } else {
            // delete entity
            $data = [
                'success' => $request->signUserOut(),
                'message' => 'User has successfully signed-out',
            ];
        }

        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
