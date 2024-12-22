<?php

declare(strict_types=1);

namespace Infrastructure\Resource;

use Doctrine\ORM\EntityManagerInterface;
use OpsWay\Doctrine\ORM\Swoole\EntityManager;
use Spatial\Core\Attributes\Injectable;
use Spatial\Entity\DbConnection;
use Spatial\Entity\DoctrineEntity;
use OpsWay\Doctrine\DBAL\Swoole\PgSQL\Scaler;
use OpsWay\Doctrine\DBAL\Swoole\PgSQL\DriverMiddleware;
use OpsWay\Doctrine\DBAL\Swoole\PgSQL\ConnectionPoolFactory;

/**
 * AppDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
#[Injectable]
class AppDB extends DBConnection
{
    public EntityManagerInterface $emApp;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        parent::__construct();
       $this->emApp = $this->connect(
            domain: 'MyApp',
            params:DoctrineConfig['dbal']['connections']['default']
        );
    }
}
