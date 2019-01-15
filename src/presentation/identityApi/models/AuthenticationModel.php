<?php
namespace Presentation\IdentityApi\Models;

/**
 * AuthenticationModel Class exists in the Api\Models namespace
 * This class to Authourized Access to Controller
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

class AuthenticationModel extends IdentityDB
{
    /**
     * Method Used to Auhtourize Access to Controller,
     * Method excepts a boolean return
     * Return false, to denied access or true to allow
     */
    private $_userLogModel;
    private $_table = 'User';

    public function onInit()
    {
        $this->_userLogModel = new UserLogModel;
        $this->_userModel = new UserModel;
        $this->_tokenModel = new TokenModel;
    }

    public function canActivate(string $url): bool
    {
        // echo \json_encode(['error'=>'access denied']);
        return true;
    }

    /**
     * Login with UserId
     * This method is not ideal.
     * Instead, learn to refresh token after expire
     *
     * @param [type] $uid
     * @return array|null
     */
    public function autoLogin($uid): ?array
    {
        // get user by Id: UserModel
        $user = $this->_userModel->getUserById($uid);

        if (!is_null($user)) {
            if ($user->activated) {

                return $this->genToken($user);

            } else {

                // record log
                $this->_userLogModel->addLog('logAccBlocked', $user->id);

                return ['result' => 0, 'message' => 'Account Blocked'];
            }
        } else {
            $this->_userLogModel->addLog('logFail');
            return ['message' => 'Invalid Username and/or Password'];
        }
    }

    /**
     * Logging in with Social Media OpenID
     *
     * @param string $email
     * @return void
     */
    public function socialEmailLogin(string $email, string $provider)
    {

        // Check both email and provider match
        $user = $this->_userModel->getUserByUsernameEmail($email);

        if (!is_null($user)) {
            //is active
            if ($user->activated) {
                if ($user->lockoutEnabled) {
                    // echo 'current time is '.strtotime(date('Y-m-d h:i:s')).' <br>time left is:: '.(strtotime(date('Y-m-d h:i:s')) - $userRow->lockout_end);
                    if ((strtotime(date('Y-m-d h:i:s')) - $user->lockoutEnd) > 0) {
                        // echo '<br> changing lock here --> '. $userRow->lockout_enabled.'<br/>';
                        $this->identityDB->table($this->_table)
                            ->where('id', $user->id)
                            ->update(
                                [
                                    'lockoutEnabled' => false,
                                    'lockoutEnd' => 0,
                                ]
                            );
                    }
                    return ['result' => 0, 'message' => 'Account At Lockdown'];
                } else {

                    return $this->genToken($user);

                }
            } else {

                // record log
                $this->_userLogModel->addLog('logAccBlocked', $user->id);
                return ['result' => 0, 'message' => 'Account Blocked'];
            }
        } else {

            // record log
            $this->_userLogModel->addLog('logFail');
            return ['message' => 'Invalid Username and/or Password'];
        }
    }

/**
 * Login
 *
 * @param string $username
 * @param string $password
 * @return array|null
 */
    public function login(string $username, string $password): ?array
    {
        $user = $this->_userModel->getUserByUsernameEmail($username);

        if (!is_null($user)) {
            //is active
            if ($user->activated) {
                if ($user->lockoutEnabled) {
                    // echo 'current time is '.strtotime(date('Y-m-d h:i:s')).' <br>time left is:: '.(strtotime(date('Y-m-d h:i:s')) - $userRow->lockout_end);
                    if ((strtotime(date('Y-m-d h:i:s')) - $user->lockoutEnd) > 0) {
                        // echo '<br> changing lock here --> '. $userRow->lockout_enabled.'<br/>';
                        $this->identityDB->table($this->_table)
                            ->where('id', $user->id)
                            ->update(
                                [
                                    'lockoutEnabled' => false,
                                    'lockoutEnd' => 0,
                                ]
                            );
                    }
                    return ['result' => 0, 'message' => 'Account At Lockdown'];
                } else {
                    if (password_verify($password, $user->hashed)) {

                        return $this->genToken($user);

                    } else {
                        //increase the atempt failed count;
                        // if atempt failed count is up to 5, shutdown/lockout

                        // record log
                        $this->_userLogModel->addLog('logFail');

                        if ($this->lockoutCount($user, false)) {
                            return ['result' => 0, 'message' => 'Password Error.'];
                        }
                    }
                }
            } else {

                // record log
                $this->_userLogModel->addLog('logAccBlocked', $user->id);
                return ['result' => 0, 'message' => 'Account Blocked'];
            }
        } else {

            // record log
            $this->_userLogModel->addLog('logFail');
            return ['message' => 'Invalid Username and/or Password'];
        }
    }

    public function genToken($user)
    {
        // if atempt failed on, turn to 0, end lockout to false.
        if ($this->lockoutCount($user, true)) {
// record log --> create dbmodel for log since it now exists elsewhere
            $this->_userLogModel->addLog('loggedIn', $user->id);

//check related entity
            $userToken = [
                'id' => $user->id,
            ];

//create separate dbmodel for entity(since it exists in media)
            $entity = (new CuratorModel)
                ->getEntityByUserId($user->id);
// if (!is_null($entity) && count($entity)) {
            //     array_merge($userToken, $entity);
            // }

//register new user to trial subscription.

            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'name' => $user->surname . ' ' . $user->othername,
                'image' => $user->image,
                'curator' => $entity,
                'idToken' => $this->_tokenModel
                    ->genToken($userToken),
                'tokenType' => 'Bearer',
                'expiresAt' => 3600,
            ];

        } else {
            return ['result' => false, 'message' => 'lockdown failed'];
        }
    }
/**
 * Lock User For Failed Attempts
 *
 * @param [type] $user
 * @param boolean $loggedIn
 * @return boolean
 */
    public function lockoutCount($user, bool $loggedIn): bool
    {
        if ($loggedIn) {
            return $this->identityDB
                ->table($this->_table)
                ->where('id', $user->id)
                ->update(
                    [
                        'accessFailedCount' => 0,
                        'lockoutEnabled' => false,
                        'lockoutEnd' => 0,
                    ]
                );
        } else {

            // echo 'failed:: '.$userRow->accessFailedCount;
            if ($user->accessFailedCount < 5) {
                $this->identityDB
                    ->table($this->_table)
                    ->where('id', $user->id)
                    ->update(['accessFailedCount' => $user->accessFailedCount + 1]);
            } elseif ($user->accessFailedCount == 5) {
                $this->identityDB
                    ->table($this->$table)
                    ->where('id', $user->id)
                    ->update(
                        [
                            'accessFailedCount' => $user->accessFailedCount + 1,
                            'lockoutEnabled' => true,
                            'lockoutEnd' => strtotime(date('Y-m-d h:i:s')) + 300,
                        ]
                    );
            } elseif ($user->accessFailedCount < 10) {
                $this->identityDB
                    ->table($this->_table)
                    ->where('id', $user->id)
                    ->update(['accessFailedCount' => $user->accessFailedCount + 1]);
            } elseif ($user->accessFailedCount == 10) {
                $this->identityDB
                    ->table($this->_table)
                    ->where('id', $user->id)
                    ->update(
                        [
                            'accessFailedCount' => false,
                            'accessFailedCount' => $user->accessFailedCount + 1,
                            'lockoutEnabled' => true,
                            'lockoutEnd' => strtotime(date('Y-m-d h:i:s')) + 86700,
                        ]
                    );
            }
            return false;
        }
    }
}
