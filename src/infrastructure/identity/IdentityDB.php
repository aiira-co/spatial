<?php

namespace Infrastructure\Identity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Spatial\Entity\DoctrineEntity;

/**
 * identityDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class IdentityDB
{
    public EntityManager $emIdentity;

    /**
     * Connect to database in constructor
     * @throws ORMException
     */
    public function __construct()
    {
        $connection = [
            'dbname' => DoctrineConfig['parameters']['database_identity_name'],
            'user' => DoctrineConfig['parameters']['database_identity_user'],
            'password' => DoctrineConfig['parameters']['database_identity_password'],
            'host' => DoctrineConfig['parameters']['database_identity_host'],
            'driver' => DoctrineConfig['parameters']['database_identity_driver'],
        ];
        $this->emIdentity = (new DoctrineEntity('identity'))
            ->entityManager($connection);
    }
}
