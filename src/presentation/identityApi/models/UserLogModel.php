<?php
namespace Presentation\IdentityApi\Models;

/**
 * UserLogModel Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

class UserLogModel extends IdentityDB
{
    private $_table = 'UserLog';

    /**
     * Log User activity
     */
    public function logUser(array $data): bool
    {
        return $this->identityDB
            ->table($this->_table)
            ->add($data);
    }

    /**
     * Log User activity
     */
    public function addLog(string $logType, int $userId): bool
    {
        switch ($logType) {
            case 'loggedIn':
                $activity = 1;
                break;
            case 'loggedOut':
                $activity = 2;
                break;

            case 'logFail':
                $activity = 3;
                break;

            case 'logAccBlocked':
                $activity = 4;
                break;

            default:
                $activity = 3;
                break;
        }

        $data = [
            'remoteAddr' => $_SERVER['REMOTE_ADDR'],
            'requestUri' => $_SERVER['REQUEST_URI'],
            'requestMethod' => $_SERVER['REQUEST_METHOD'],
            'userId' => $userId,
            'activityId' => $activity,
        ];

        return $this->identityDB
            ->table($this->_table)
            ->add($data);
    }
}
