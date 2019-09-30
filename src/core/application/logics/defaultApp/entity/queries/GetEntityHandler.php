<?php

namespace Core\Application\Logics\DefaultApp\Queries;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;
use Spatial\Psr7\Response;

class GetEntityHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        // Return response
        $payload = json_encode($request->getPersons());
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
