<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Commands;

use Core\Domain\Identity\Person;
use Cqured\MediatR\IRequest;
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class DeleteUser extends Request
{

    public function deleteUser(int $id)
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;
        // get all
        $UserRepository = $this->emIdentity
            ->getRepository(Person::class);
        $Users = $UserRepository->findAll();

        return [$Users];
    }
}
