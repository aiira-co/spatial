<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Queries;

use Core\Domain\Identity\Person;
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class Profile extends Request
{

    public $username;

    public function getUser()
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;

        $user = $this->emIdentity
            ->getRepository(Person::class)
            ->findOneBy(['username' => $this->username]);
        return [
            'id' => $user->getId(),
            'image' => $user->getImage(),
            'cover' => $user->getCover(),
            'username' => $user->getUsername(),
            'tagline' => $user->getTagline(),
            'name' => $user->getName(),
            'location' => $user->getLocation(),
        ];
    }
}
