<?php

namespace Infrastructure\Resource;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use OpsWay\Doctrine\DBAL\Swoole\PgSQL\ConnectionPoolFactory;
use OpsWay\Doctrine\DBAL\Swoole\PgSQL\DriverMiddleware;
use OpsWay\Doctrine\DBAL\Swoole\PgSQL\Scaler;
use Spatial\Entity\DoctrineEntity;
use OpsWay\Doctrine\ORM\Swoole\EntityManager;

/**
 * AppDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class AppDB
{
    public EntityManagerInterface $emApp;

    public function __construct()
    {
        $doctrine = new DoctrineEntity('myapp');
        $connectionParams = DoctrineConfig['doctrine']['dbal']['connections']['default'];

        if ($connectionParams['driverClass'] === '\OpsWay\Doctrine\DBAL\Swoole\PgSQL\Driver') {
            $pool = (new ConnectionPoolFactory())($connectionParams);
            $doctrine->getDoctrineConfig()->setMiddlewares([new DriverMiddlewareware($pool)]);
            $scaler = new Scaler($pool, $connectionParams['tickFrequency']); // will try to free idle connect on connectionTtl overdue
        }

        $this->emApp = new EntityManager(fn()=>$doctrine->entityManager($connectionParams));

        // or
        // $connection = [
        //     'dbname' => 'mydatabase',
        //     'user' => 'postgres',
        //     'password' => 'password',
        //     'host' => '127.0.0.1',
        //     'driver' => 'pdo_pgsql',
        // ];
        // $this->emArtist = (new DoctrineEntity('artist'))
        //     ->setProxyDir('./src/core/domain/artist/proxies')
        //     ->setProxyNamespace('Core\Domain\Artist')
        //     ->entityManager($connection);


    }
}
