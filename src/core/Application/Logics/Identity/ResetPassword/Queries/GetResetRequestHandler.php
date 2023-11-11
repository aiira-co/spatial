<?php

namespace Core\Application\Logics\Identity\ResetPassword\Queries;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

class GetResetRequestHandler extends RequestHandler
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
        $data = [];
        if ($request->requestTokenExists() && $request->isTokenActive()) {
            $data = $request->getResetPasswordRequest();
        }


        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}