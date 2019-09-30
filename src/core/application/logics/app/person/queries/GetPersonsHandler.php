<?php

namespace Core\Application\Logics\App\Queries;

use Cqured\Mediator\IRequest;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetPersons implements IRequest
{
    public function getPersons()
    {
        return  [
            'id' => 3,
            'data' => [
                ['name' => 'Amma', 'gender' => 'female'],
                ['name' => 'Kwasi', 'gender' => 'male'],
            ],
            'expire' => null,
        ];
    }
}
