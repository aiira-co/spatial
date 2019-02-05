<?php
namespace Infrastructure\Resource;

use Cqured\Entity\DoctrineEntity;

/**
 * AppDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class AppDB
{
    public $emApp;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $connection = ['url' => 'mysql://db_user:db_password@localhost/db_name'];
        $this->emMedia = (new DoctrineEntity)
            ->setProxyDir('/src/core/domain/myapp/proxies')
            ->setProxyNamespace('Core\Domain\MyApp')
            ->entityManager($connection);

    }

    /**
     * OnInit()
     *
     * @return void
     */
    public function onInit()
    {
    }

}
