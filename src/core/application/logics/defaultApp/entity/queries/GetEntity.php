<?php

namespace Core\Application\Logics\DefaultApp\Queries;

use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetEntity extends Request
{
    public function getPersons()
    {
        return  [
            'params' => $this->getServerParams(),
            'data' => [
                ['name' => 'Amma', 'gender' => 'female'],
                ['name' => 'Kwasi', 'gender' => 'male'],
            ],
            'expire' => null,
        ];
    }
}
