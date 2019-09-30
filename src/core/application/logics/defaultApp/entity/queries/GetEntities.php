<?php

namespace Core\Application\Logics\DefaultApp\Queries;

use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetEntites extends Request
{
    public function getPersons(): array
    {
        return  [
            'total' => 10,
            'page' => 1,
            'pageSize' => 20,
            'data' => [
                ['name' => 'Amma', 'gender' => 'female'],
                ['name' => 'Kwasi', 'gender' => 'male'],
                ['name' => 'Amma', 'gender' => 'female'],
                ['name' => 'Kwasi', 'gender' => 'male'],
                ['name' => 'Amma', 'gender' => 'female'],
                ['name' => 'Kwasi', 'gender' => 'male'],
                ['name' => 'Kwasi', 'gender' => 'male'],
            ]
        ];
    }
}
