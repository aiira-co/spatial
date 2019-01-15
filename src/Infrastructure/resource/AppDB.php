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
        $connection = ['url' => 'mysql://root:glory@localhost/mediadata'];
        $this->emMedia = (new DoctrineEntity)
            ->setProxyDir('/src/core/domain/media/proxies')
            ->setProxyNamespace('Core\Domain\Media')
            ->entityManager($connection);
        $this->onInit();
    }

    /**
     * OnInit()
     *
     * @return void
     */
    public function onInit()
    {
    }

    /**
     * For Debugging, this class returns the recent sql statement queried
     *
     * @return string
     */
    public function getSQL(): string
    {
        return $this->airMediaDB->sql;
    }

    /**
     * Get Last Inserted ID
     *
     * @return integer
     */
    public function getLastId(): int
    {
        return $this->airMediaDB->postId;
    }
}
