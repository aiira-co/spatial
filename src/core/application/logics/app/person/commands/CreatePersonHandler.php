<?php
namespace Core\Application\Logics\Myapp\Commands;

use Cqured\Mediator\IRequest;
use Cqured\Mediator\IRequestHandler;
use Cqured\Mediator\Response;

class CreatePersonHandler implements IRequestHandler
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
