<?php

declare(strict_types=1);

namespace Infrastructure\Resource;

use Doctrine\ORM\EntityManagerInterface;
use OpsWay\Doctrine\ORM\Swoole\EntityManager;
use Doctrine\ORM\ORMException;
use Spatial\Core\Attributes\Injectable;
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
class AppDB
{
    public EntityManagerInterface $emApp;
    public \Closure $entityManager;

    /**
     * Connect to database in constructor
     * @throws ORMException
     */
    public function __construct()
    {
        $doctrine = new DoctrineEntity('pixbay');
        $connectionParams = DoctrineConfig['doctrine']['dbal']['connections']['defaultDB'];

        if ($connectionParams['driverClass'] === '\OpsWay\Doctrine\DBAL\Swoole\PgSQL\Driver') {
            $pool = (new ConnectionPoolFactory())($connectionParams);
            $doctrine->getDoctrineConfig()->setMiddlewares([new DriverMiddleware($pool)]);
            $scaler = new Scaler($pool, $connectionParams['tickFrequency']); // will try to free idle connect on connectionTtl overdue
        }
        $this->entityManager = fn() => $doctrine->entityManager($connectionParams);
        $this->emApp = new EntityManager($this->entityManager);

//        $this->emSocial = $doctrine->entityManager($connectionParams);

//        $connectionParams['driver']='pdo_pgsql';
//        $this->emSocial = $doctrine->entityManager($connectionParams);
    }
}
