<?php

declare(strict_types=1);

namespace Infrastructure\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Spatial\Core\Attributes\Injectable;
use Spatial\Entity\DbConnection;

/**
 * SuiteDB Class exists in the Api\Models namespace
 * A Model interacts with the database and returns the results to a Controller.
 *
 * @category Model
 */
#[Injectable()]
class AppDB extends DbConnection
{
    public EntityManagerInterface $emApp;

    public function __construct()
    {
        // Call parent constructor with the pool ID
        parent::__construct(
            poolId: 'app_db_pool', // Define a unique pool ID for this database connection
            domain:'MyApp',
            params:DoctrineConfig['doctrine']['dbal']['connections']['default']
        );

        // Acquire a connection from the pool
        $this->emApp = $this->getConnection();
    }

    /**
     * Initialize any specific logic if needed.
     */
    public function onInit(): void
    {
        // Add custom initialization logic here, if necessary.
    }

    /**
     * Release the connection back to the pool on destruction.
     */
    public function __destruct()
    {
        if (isset($this->emApp)) {
            $this->releaseConnection($this->emApp);
        }
    }
}
