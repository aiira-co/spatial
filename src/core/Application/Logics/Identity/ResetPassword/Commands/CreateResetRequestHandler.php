<?php

namespace Core\Application\Logics\Identity\ResetPassword\Commands;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class CreateResetRequestHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Return response
        // check username & email again
        if (!$request->personExists()) {
            $data = [
                'success' => false,
                'message' => 'Email will be sent if user is found',
            ];
        } elseif (!$request->isAccountActive()) {
            $data = [
                'success' => false,
                'message' => 'Account is Temporally Blocked. Please try again later',
            ];
        } else {
            $data = [
                'success' => $request->createResetRequest(),
                'message' => 'Email will be sent if user is found!'
            ];
        }

        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}