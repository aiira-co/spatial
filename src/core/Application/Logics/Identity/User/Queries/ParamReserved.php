<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
use Doctrine\ORM\ORMException;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class ParamReserved extends Request
{
    use IdentityTrait;

    public object $data;

    /**
     * @return bool
     * @throws ORMException
     */
    public function checkParam(): bool
    {
        $this->getEntityManager();

        $user = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t')
            ->where('lower(t.' . $this->data->field . ') = lower(:name)')
            ->setParameter('name', trim($this->data->value))
            ->getQuery()
            ->execute();

        return $user ? true : false;
    }
}
