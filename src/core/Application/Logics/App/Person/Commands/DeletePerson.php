<?php

namespace Core\Application\Logics\App\Person\Commands;

use Cqured\Mediator\IRequest;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class DeletePerson implements IRequest
{
    public function addPerson()
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
