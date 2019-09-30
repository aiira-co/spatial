<?php

namespace Core\Application\Logics\DefaultApp\Commands;

use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class CreateEntity extends Request
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
        return $this->emMedia->getRepository(Media::class);
    }
}
