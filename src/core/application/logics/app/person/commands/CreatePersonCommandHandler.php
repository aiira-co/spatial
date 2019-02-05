<?php
namespace Core\Application\Logics\Myapp\Commands;

use Cqured\MediatR\IRequest;
use Cqured\MediatR\IRequestHandler;
use Cqured\MediatR\Response;

class CreatePersonCommandHandler implements IRequestHandler
{
    /**
     * Handles Server Response
     *
     * @param OneWay $request
     * @return Response
     */
    public function handle(IRequest $request)
    {
        // Return response
        return $request->getPersons();
    }
}
