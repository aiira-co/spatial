<?php
namespace Presentation\IdentityApi\Models;
/**
 * AirMediaDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class CuratorModel extends AirMediaDB
{
    private $_table = 'Entity';

    /**
     * Get Entities Connected to the user
     *
     * @param integer $id
     * @return array|null
     */
    public function getEntityByUserId(int $id): ?array
    {
        return $this->airMediaDB
            ->table($this->_table)
            ->where('ownerId', $id)
            ->andWhere('activated', true)
            ->fields('t.id, t.entityTypeId as type')
            ->get();
    }

}
