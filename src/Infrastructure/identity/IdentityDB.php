<?php
namespace Infrastructure\Identity;

use Cqured\Entity\DoctrineEntity;

/**
 * identityDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class IdentityDB
{
    public $emIdentity;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $connection = ['url' => 'mysql://root:glory@localhost/galaxyId'];
        $this->emIdentity = (new DoctrineEntity('identity'))
            ->setProxyDir('./src/core/domain/identity/proxies')
            ->setProxyNamespace('Core\Domain\Identity')
            ->entityManager($connection);

    }

}
