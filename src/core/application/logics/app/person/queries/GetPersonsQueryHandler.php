<?php
namespace Core\Application\Logics\App\Queries;

use Core\Domain\Media\Media;
use Cqured\MediatR\IRequest;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetPersonsQuery implements IRequest
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
        return $this->emMedia->getRepository(Media::class);
    }
}
