<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Core\Domain\Identity\Person;
use Cqured\MediatR\IRequest;
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetUser extends Request
{
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

        return true;
    }

    /**
     * Get Basic User Info
     *
     * @return array
     */
    public function getUser(): array
    {
        return [
            'id' => $this->_user->getId(),
            'image' => $this->_user->getImage(),
            'username' => $this->_user->getUsername(),
            'surname' => $this->_user->getSurname(),
            'othername' => $this->_user->getOthername(),
        ];
    }

    /**
     * User Profile for Settings
     *
     * @return array
     */
    public function getUserProfile(): array
    {
        return [
            'username' => $this->_user->getUsername(),
            'surname' => $this->_user->getSurname(),
            'othername' => $this->_user->getOthername(),
            'gender' => $this->_user->getGender()->getId(),
            'accountTypeId' => $this->_user->getAccountType()->getId(),
            'bio' => $this->_user->getBio(),
            'tagline' => $this->_user->getTagline(),
            'location' => $this->_user->getLocation(),
            'language' => $this->_user->getLanguage(),
            'links' => $this->_user->getLinks(),
            // 'timezone' => $this->_user->getTimezone(),
        ];
    }

    /**
     * User Avatar
     *
     * @return array
     */
    public function getUserAvatar(): array
    {
        return [
            'image' => $this->_user->getImage(),
        ];
    }

    /**
     * User Email Veri & Phone Veri
     *
     * @return array
     */
    public function getUserContact(): array
    {
        return [
            'email' => $this->_user->getEmail(),
            'emailVerified' => $this->_user->getEmailVerified(),
            'phone' => $this->_user->getPhoneOne(),
            'phoneVerified' => $this->_user->getPhoneVerified(),
        ];
    }

    /**
     * Notification Alerts
     *
     * @return array
     */
    public function getUserNotification(): array
    {
        return [
            'image' => $this->_user->getImage(),
        ];
    }

    /**
     * Connect Apps for User
     *
     * @return array
     */
    public function getUserAppClaims(): array
    {
        return [
            'image' => $this->_user->getImage(),
        ];
    }

    /**
     * User Account Info
     *
     * @return array
     */
    public function getUserAccount(): array
    {
        //get plan for the appClaim. basic / member
        return [
            'created' => $this->_user->getCreated(),
            'type' => $this->_user->getImage(),
        ];
    }
}
