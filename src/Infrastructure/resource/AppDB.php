<?php

namespace Infrastructure\Resource;

use Spatial\Entity\DoctrineEntity;

/**
 * AppDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class AppDB
{
    public $emApp;

    public function __construct()
    {
        $connection = ['url' => 'mysql://root:glory@localhost/mydatabase'];
        $this->emApp = (new DoctrineEntity('myapp'))
            ->entityManager($connection);

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
