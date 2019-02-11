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

    public function __construct()
    {
        $connection = ['url' => 'mysql://root:glory@localhost/galaxyId'];
        $this->emApp = (new DoctrineEntity('myapp'))
            ->entityManager($connection);

    }

}
