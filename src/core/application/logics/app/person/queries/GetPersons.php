<?php

namespace Core\Application\Logics\App\Queries;

use Cqured\Mediator\IRequest;
use Cqured\Mediator\IRequestHandler;
use Cqured\Mediator\Response;

class GetPersonsHandler implements IRequestHandler
{
    /**
     * Handles Server Response
     *
     * @param IRequest $request
     * @return Response
     */
    public function handle(IRequest $request)
    {
        // Return response
        return $request->getPersons();
    }
}
