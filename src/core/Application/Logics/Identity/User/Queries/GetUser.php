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
    public int $id;
    public object $params;

    private Person $_user;

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
            'id' => $this->_user->id,
            'image' => $this->_user->image,
            'username' => $this->_user->username,
            'surname' => $this->_user->surname,
            'othername' => $this->_user->othername,
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
            'username' => $this->_user->username,
            'surname' => $this->_user->surname,
            'othername' => $this->_user->othername,
            'gender' => $this->_user->gender->id,
            'accountTypeId' => $this->_user->accountType->id,
            'bio' => $this->_user->bio,
            'tagline' => $this->_user->tagline,
            'location' => [
                'city' => $this->_user->city,
                'country' => $this->_user->country
            ],
            'language' => $this->_user->language,
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
            'image' => $this->_user->image,
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
            'email' => $this->_user->email,
            'emailVerified' => $this->_user->emailVerified,
            'phone' => $this->_user->phoneOne,
            'phoneVerified' => $this->_user->phoneVerified,
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
            'image' => $this->_user->image,
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
            'image' => $this->_user->image,
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
            'created' => $this->_user->created->getTimestamp(),
            'type' => $this->_user->image,
        ];
    }
}
