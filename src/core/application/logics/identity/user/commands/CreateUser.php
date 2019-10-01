<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Commands;

use Core\Domain\Identity\{Groups, Person};
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class CreateUser extends Request
{
    public $data = [];
    private $_acType = 41; // Individual Account Type
    private $_lang = 'en'; // Individual Account Type

    public function createUser()
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;

        $user = new Person();
        // $accountType 41 for Individual, 42 = Organization, 43 A, 44 Cr
        $accountType = $this->emIdentity->find(Groups::class, $this->_acType);
        // $language = $this->emIdentity->find(Groups::class, $this->_lang);
        //create account phase 1
        $user->setTagline('I Am New Here');
        $user->setBio('About Myself');

        $user->setUsername($this->data->username);
        $user->setImage('avatar/default.jpg');
        $user->setEmail($this->data->email);
        $user->changePassword($this->data->password);

        $user->setCreated(new \DateTime("now"));

        $user->setEmailVerified(false);
        $user->setPhoneVerified(false);

        $user->setActivated(true);
        $user->setLockoutEnabled(false);

        $user->setAccessFailedCount(0);
        $user->setAccountType($accountType);
        $user->setLanguage($this->_lang);

        $user->setLocation('Accra', 'GH');
        $user->setTimezone('UTC 0:00');

        $this->emIdentity->persist($user);
        $this->emIdentity->flush();

        return $user->getId();

        // return $this->emIdentity->getRepository(Identity::class);
    }
}
