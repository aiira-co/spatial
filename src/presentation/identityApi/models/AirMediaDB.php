<?php
namespace Presentation\IdentityApi\Models;

use Cqured\Entity\EntityModel;

/**
 * AirMediaDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class AirMediaDB
{
    protected $airMediaDB;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname=airMediaDB;host=127.0.0.1';
        $user = 'root';
        $password = 'glory';
        $this->airMediaDB = new EntityModel($dsn, $user, $password);
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
