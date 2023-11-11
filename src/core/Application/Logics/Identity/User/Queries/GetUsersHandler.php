<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Common\Libraries\SearchAlg;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

use function json_encode;

class GetUsersHandler extends RequestHandler
{
    /**
     * Handles Server Response
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $request->searchAlg = new SearchAlg($request->params->searchValue ?? null);

        // Return response
        $criteria = [];

        $offset = ($request->params->page - 1) * $request->params->pageSize;
        
        $request->getEntityManager();

        $passCount = false;


        // echo 'sdff';
        $data = $request->getUsers(
            $criteria,
            $request->params->orderBy ?? ['id'],
            (int)$request->params->pageSize,
            $offset
        );
        // Return response
        $data = [
            'data' => $data,
            'page' => (int)$request->params->page,
            'pageSize' => count($data) ?? 0,
            'total' => $request->countTotalUsers($criteria),
        ];

        $payload = json_encode($data ?? [], JSON_THROW_ON_ERROR, 512);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
