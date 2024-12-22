<?php

namespace Infrastructure\Identity;

use Doctrine\ORM\EntityManagerInterface;
use Spatial\Core\Attributes\Injectable;
use Spatial\Entity\DbConnection;

/**
 * identityDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
#[Injectable]
class IdentityDB extends DBConnection
{
    public EntityManagerInterface $emIdentity;

    public function __construct()
    {
        parent::__construct();

        $this->emIdentity = $this->connect(
            domain: 'identity',
            params:DoctrineConfig['dbal']['connections']['identity']
        );

    }
}
