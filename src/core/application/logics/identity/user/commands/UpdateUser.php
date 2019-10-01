<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Commands;

use Core\Domain\Identity\{Groups, Person};
use Cqured\MediatR\IRequest;
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class UpdateUser extends Request
{
    public $data = [];
    public $id;
    public $params;

    private $_user;

    /**
     * Check if User exists
     *
     * @return boolean
     */
    public function userExist(): bool
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;

        $user = $this->emIdentity->find(Person::class, $this->id);

        if (!$user) {
            return false;
        }

        $this->_user = $user;
        // $this->data->id = $user->getId();

        return true;
    }

    /**
     * Quick Update User @ Setup
     *
     * @return boolean|null
     */
    public function updateUser(): ?bool
    {
        // $user = $this->emIdentity->find(User::class, (int) $this->id);
        $gender = $this->emIdentity->find(Groups::class, $this->data->gender);

        if (!($this->_user && $gender)) {
            return false;
        }

        //create account phase 1
        $this->_user->setSurname($this->data->surname);
        $this->_user->setOthername($this->data->othername);
        $this->_user->setGender($gender);

        $this->_user->setBirthdate(new \DateTime($this->data->birthDate));
        $this->_user->setPhoneOne($this->data->phoneOne);

        $this->emIdentity->flush();

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * Get User App Claims for App Updates
     *
     * @return array
     */
    public function getUserAppClaims(): array
    {
        $appClaims = [];

        foreach ($this->_user->getAppClaims() as $claim) {
            if ($claim->getActivated()) {
                // echo $claim->getApp()->getId();
                array_push($appClaims, strtolower($claim->getApp()->getName()));
            }
        }

        // print_r($appClaims);

        return $appClaims;
    }

    /**
     * Get Logged User Basic Info (array)
     *
     * @return array
     */
    public function getPersonInfo(): array
    {
        return [
            'id' => $this->_user->getId(),
            'username' => $this->_user->getUsername(),
            'image' => $this->_user->getImage(),
            'email' => $this->_user->getEmail(),
            'name' => $this->_user->getName(),
            'cover' => $this->_user->getCover(), // for social
            'tagline' => $this->_user->getTagline(), // for social
        ];
    }

    /**
     * Update User Avatar
     *
     * @return boolean|null
     */
    public function updateUserAvatar(): ?bool
    {
        // $user = $this->emIdentity->find(User::class, (int) $this->id);

        //create account phase 1
        $this->_user->setImage($this->data->image);

        $this->emIdentity->flush();

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * Update user cover image
     *
     * @return boolean|null
     */
    public function updateUserCover(): ?bool
    {
        // $user = $this->emIdentity->find(User::class, (int) $this->id);

        //create account phase 1
        $this->_user->setCover($this->data->cover);

        $this->emIdentity->flush();

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * Update User Password
     *
     * @return boolean
     */
    public function updateUserPassword(): bool
    {
        // first auth that current password is correct
        if ($this->_user->authenticate($this->data->cpass)) {
            // change  pass word
            $this->_user->changePassword($this->data->npass);
            $this->emIdentity->flush();

            return true;
        }

        return false;
    }

    /**
     * Update User Info
     *
     * @return boolean|null
     */
    public function updateUserProfile(): ?bool
    {
        // accountType: 41
        // bio: null
        // gender: 45
        // language: "en"
        // links: {}
        // location: {city: "Kumasi", country: "GH"}
        // othername: "Kofi"
        // surname: "Owusu-Afriyie"
        // tagline: "x"
        // username: "majesty"
        //     }
        $gender = $this->emIdentity->find(Groups::class, (int) $this->data->gender);
        $accountType = $this->emIdentity->find(Groups::class, (int) $this->data->accountTypeId);
        // $timezone = $this->emIdentity->find(Groups::class, $this->data->timezone);
        // $language = $this->emIdentity->find(Language::class, $this->data->language);
        // $country = $this->emIdentity->find(Language::class, $this->data->location->country);

        $this->_user->setUsername($this->data->username);
        $this->_user->setSurname($this->data->surname);
        $this->_user->setOthername($this->data->othername);

        $this->_user->setGender($gender);

        $this->_user->setTagline($this->data->tagline);
        $this->_user->setAccountType($accountType);
        if (!is_null($this->data->bio)) {
            $this->_user->setBio($this->data->bio);
        }

        $this->_user->setLocation($this->data->location->city, $this->data->location->country);
        $this->_user->setTimezone($this->data->timezone);
        $this->_user->setLanguage($this->data->language);

        // set Links
        // $linksCount = count($this->data->links);
        // $linkKeys = array_keys($this->data->links);
        // for ($i = 0; $i < $linksCount; $i++) {
        //     // 51 twitter
        //     // 52 facebook
        //     // 53 google
        //     // 54 linkedin
        //     // 55 galaxy
        //     // 56 site

        //     switch ($variable) {
        //         case 'value':
        //             # code...
        //             break;

        //         default:
        //             # code...
        //             break;
        //     }
        // }

        // links
        // 51 twitter
        // 52 facebook
        // 53 google
        // 54 linkedin
        // 55 galaxyId

        $this->emIdentity->flush();

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * Update User Account Info
     * Tickets to delete account
     * Upgrade AppClaim Person Plan/Status
     *
     *
     * @return void
     */
    public function updateUserAccount()
    {
        // $user = $this->emIdentity->find(User::class, (int) $this->id);

        //create account phase 1
        $this->_user->setCover($this->data->cover);

        $this->emIdentity->flush();

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }
}
