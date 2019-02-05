<?php
namespace Presentation\IdentityApi\Models;

/**
 * UserModel Class exists in the Api\Models namespace
 * This class to Authourized Access to Controller
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

class UserModel extends IdentityDB
{
    private $_table = 'User';
    public $userId;

    public function getUserById(int $id)
    {
        return $this->identityDB
            ->table($this->_table)
            ->where('id', $id)
            ->single();
    }
  

    public function getUserByUsernameEmail(string $key)
    {
        return $this->identityDB
            ->table($this->_table)
            ->where('username', $key)
            ->orWhere('email', $key)
            ->single();
    }

    public function countPerson(string $field, string $string): int
    {
        return $this->identityDB
            ->table($this->_table)
            ->where($field, $string)
            ->count();
    }

    public function addData(array $data): bool
    {
        return $this->identityDB
            ->table($this->_table)
            ->add($data);
    }

    public function initTrialSubscription(int $userId)
    {
        $data = [
            'userId' => $userId,
            'activated' => true,
            'date' => strtotime(date('Y-m-d h:i:s')),
            'lastActiveTime' => strtotime(date('Y-m-d h:i:s')),
            'subscriptionId' => 1,
        ];
        return $this->identityDB
            ->table('UserSubscription')
            ->add($data);
    }

    public function updateData(array $data, int $id): bool
    {
        return $this->identityDB
            ->table($this->_table)
            ->where('id', $id)
            ->update($data);
    }

    public function getUserId(): int
    {
        return $this->identityDB
            ->postId;
    }
}
