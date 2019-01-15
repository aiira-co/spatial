<?php
namespace Presentation\IdentityApi\Controllers;

// use Api\Controller\BaseController;
use Presentation\IdentityApi\Models\UserModel;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */

class AccountController extends BaseController
{

    // method called to handle a GET request
    public function onInit()
    {
        $this->_userModel = new UserModel;
    }
    public function httpGet(int...$id): ?array
    {
        $result = $this->personExists($this->params->searchValue, $this->params->searchField);

        return ['result' => $result];
    }

    public function personExists(string $string, $field): bool
    {
        if ($this->_userModel->getUserByUsernameEmail($string, $field)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * POST request
     *
     * @param array $form
     * @return array|null
     */
    public function httpPost(array $form): ?array
    {
        // after singing up, authenticate then send to next screen
        //check email again.
        if (!$this->personExists($form['email'], 'email') && !$this->personExists($form['username'], 'username')) {
            return $this->signup($form);
            // return ['result'=>$this->personExists($form['email'],'email') && $this->personExists($form['username'],'username')];
        } else {
            return ['result' => 0, 'message' => 'Email / Username Already Exists'];
        }
    }

    /**
     * Register New User
     *
     * @param array $user
     * @return array|null
     */
    public function signup(array $user): ?array
    {
        //check if username exists

        $data = [
            'username' => trim($user['username']),
            'email' => trim($user['email']),
            'hashed' => password_hash($user['hashed'], PASSWORD_DEFAULT),
            'emailVerified' => false,
            'activated' => true,
            'phoneVerified' => false,
            'provider' => 'air',
        ];

        // now, authenticate the user
        // $this->genToken(DB::$postId);
        // /subscribe
        if ($this->_userModel->addData($data) & $this->_userModel->initTrialSubscription($this->_userModel->getUserId())) {
            // Send Mail to Email to verify
            return [
                'result' => true,
                'message' => 'Welcome ' . trim($user['username']) . '. You have 24 hours Trial period',
            ];
        } else {
            return [
                'result' => false,
                'message' => 'Error Occured with db post',
            ];
        }
    }

    /**
     * _token
     *
     * @param integer $id
     * @return string
     */
    private function _genToken(int $id): string
    {
        $config = require 'config.php';
        $now = new DateTimeImmutable();
        $tokenGen = $config->createBuilder()
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->issuedBy('http://foo.bar')
            ->permittedFor('http://client1.bar')
            ->permittedFor('http://client2.bar')
            ->permittedFor('http://client3.bar')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+30 seconds'))
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('userId', $id)
            ->getToken($config->getSigner(), $config->getSigningKey());

        return (string) $token;
    }
    /**
     * Update Info
     *
     * @param array $form
     * @param integer $id
     * @return array|null
     */
    public function httpPut(array $form, int $id): ?array
    {
        // code here
        return ['status' => $this->_userModel->updateData($form, $id)];
    }
}
