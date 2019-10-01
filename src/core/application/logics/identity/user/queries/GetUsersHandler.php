<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Queries;

use Core\Application\Logics\Model\SearchAlg;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spatial\Psr7\RequestHandler;

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

        // searchAlg
        $searchQuery = $request->params->searchValue ?? null;
        if (!is_null($searchQuery) &&  trim($searchQuery) !== '') {
            $searchQuery = (new SearchAlg())->wordSearch($searchQuery);
        } else {
            $searchQuery = '';
        }

        // Return response
        $criteria = [

            'search' => $searchQuery,
            'type' => (int) $request->params->category ?? 39,
        ];

        $offset = ($request->params->page - 1) * $request->params->pageSize;

        $data = $request->getUsers(
            $criteria,
            $request->params->orderBy ?? null,
            $request->params->pageSize,
            $offset
        );
        // Return response
        $data = [
            'data' => $data,
            'page' => (int) $request->params->page,
            'pageSize' => count($data) ?? 0,
            'total' => (int) $request->countTotalUsers($criteria),
        ];

        $payload = \json_encode($data ?? []);
        $this->response->getBody()->write($payload);
        return $this->response;
    }
}
