<?php

namespace Core\Application\Logics\Identity\ResetPassword\Commands;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class UpdateResetRequestHandler extends RequestHandler
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
        if (!$request->requestExists()) {
            $data = [
                'success' => false,
                'message' => 'Request Token was not found',
            ];
        } elseif ($request->isOldPassword()) {
            $data = [
                'success' => false,
                'message' => 'Old Password cannot be used. Please create new Password',
            ];
        } else {
            $data = [
                'success' => $request->updatePasswordRequest(),
                'message' => 'Password successfully been updated'
            ];
        }

        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}