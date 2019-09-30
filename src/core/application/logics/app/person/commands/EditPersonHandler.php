<?php

namespace Core\Application\Logics\App\Commands;

use Cqured\Mediator\IRequest;
use Cqured\Mediator\IRequestHandler;
use Cqured\Mediator\Response;

class UpdatePersonHandler implements IRequestHandler
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
