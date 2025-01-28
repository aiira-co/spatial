<?php

declare(strict_types=1);

namespace Infrastructure\Identity;

use Doctrine\ORM\EntityManagerInterface;
use OpenSwoole\Coroutine;
use Spatial\Core\Attributes\Injectable;
use Spatial\Entity\DbConnection;

/**
 * IdentityDB Class exists in the Api\Models namespace
 * A Model interacts with the database and returns the results to a Controller.
 *
 * @category Model
 */
#[Injectable('request')]
class IdentityDB extends DbConnection
{
    public EntityManagerInterface $emIdentity;

    public function __construct()
    {
        // Call parent constructor with the pool ID
        parent::__construct(
            poolId: 'identity_db_pool', // Define a unique pool ID for this database connection
            domain:'identity',
            params:DoctrineConfig['doctrine']['dbal']['connections']['identity']
        );

        // Acquire a connection from the pool
        $this->emIdentity = $this->getConnection();
    }

    /**
     * Custom logic for closing the connection.
     */
    public function close(): void
    {
        if (isset($this->emIdentity)) {
            $this->emIdentity->close();
        }
    }

    /**
     * Release the connection back to the pool on destruction.
     */
    public function __destruct()
    {
        $this->cleanup();
    }

    private function cleanup(){
        Coroutine::create(function(){
            if (isset($this->emIdentity)) {
                $this->releaseConnection($this->emIdentity);
            }
        });
    }
}
