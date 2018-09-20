<?php
namespace Api\Models;

/**
 * PracticeModel Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

use Lynq\Entity\EntityModel;

class PracticeModel
{
    private $_table = 'Media';

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname=airMediaDB;host=127.0.0.1';
        $user = 'root';
        $password = 'glory';
        $this->mediaDB = new EntityModel($dsn, $user, $password);
    }

    /**
     * Get ALL items via search $key
     */
    public function getItems($key)
    {
        return $this->mediaDB->table($this->_table)
                      ->where('title', 'LIKE', '%'.$key.'%')
                      ->orderBy('id')
                      ->get();
    }

    /**
     * Get SINGLE items via $id
    */
    public function getItem(int $id)
    {
        return $this->mediaDB->table($this->_table)->where('id', $id)->single();
    }

    /**
     * Count All items
    */
    public function countItems()
    {
        return $this->mediaDB->table($this->_table)
                    ->count();
    }
    /**
         * Insert New items, pass items as array $data
         */
    public function addItem(array $data):bool
    {
        return $this->mediaDB->table($this->_table)->add($data);
    }
    /**
         * Update item, pass items as array $data to alter of the item $id
         */
    public function updateItem(array $data, int $id):bool
    {
        return $this->mediaDB->Table($this->_table)->where('id', $id)->update($data);
    }

    /**
         * Delete Item by $id
         */
    public function deleteItem(int $n):bool
    {
        return $this->mediaDB->table($this->_table)->where('id', $n)->delete();
    }

    /**
     * For Debugging, this class returns the recent sql statement queried
     */
    public function getSQL(): string
    {
        return $this->mediaDB->$sql;
    }
}
