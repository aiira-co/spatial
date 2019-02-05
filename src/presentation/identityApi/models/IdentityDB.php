<?php
namespace Presentation\IdentityApi\Models;

/**
 * PracticeModel Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

use Cqured\Entity\EntityModel;

class IdentityDB
{
    protected $identityDB;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname=airIdentityManageDB;host=127.0.0.1';
        $user = 'root';
        $password = 'glory';
        $this->identityDB = new EntityModel($dsn, $user, $password);
        $this->onInit();
    }

    public function onInit()
    {
    }

    /**
     * For Debugging, this class returns the recent sql statement queried
     */
    public function getSQL(): string
    {
        return $this->mediaDB->$sql;
    }
}
