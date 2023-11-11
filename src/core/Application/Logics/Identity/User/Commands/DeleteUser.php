<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Commands;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
use Doctrine\ORM\ORMException;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class DeleteUser extends Request
{

    use IdentityTrait;

    public int $id;
    private ?Person $person;

    /**
     * @param int $id
     * @return array
     * @throws ORMException
     */
    public function deleteUser(): array
    {
        $this->getEntityManager();
        // get all
        $this->person = $this->emIdentity
            ->find(Person::class, $this->id);

        if ($this->person === null) {
            return [
                'success' => false,
                'message' => 'Person was not found'
            ];
        }

        $this->person->activated = false;
        $this->emIdentity->persist($this->person);
        $this->emIdentity->flush();
        return [
            'success' => true,
            'message' => 'Person successfully deleted'
        ];

    }
}
