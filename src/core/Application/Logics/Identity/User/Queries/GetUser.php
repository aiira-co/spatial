<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Queries;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class GetUser extends Request
{
    use IdentityTrait;

    public int $id;
    private ?Person $user;

    /**
     * Check if User exists
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function userExist(): bool
    {
        $this->getEntityManager();
        $this->user = $this->emIdentity->find(Person::class, $this->id);
        return $this->user !== null;
    }

    /**
     * Get Basic User Info
     *
     * @return array
     */
    public function getUser(): array
    {
        return [
            'id' => $this->user->id,
            'image' => $this->user->image,
            'username' => $this->user->username,
            'surname' => $this->user->surname,
            'othername' => $this->user->othername,
            'isVerified' => $this->user->isVerified,
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
            'username' => $this->user->username,
            'surname' => $this->user->surname,
            'othername' => $this->user->othername,
            'gender' => $this->user->gender,
            'accountTypeId' => $this->user->accountType,
            'bio' => $this->user->bio,
            'tagline' => $this->user->tagline,
            'location' => [
                'city' => $this->user->city,
                'country' => $this->user->country
            ],
            'language' => $this->user->language,
            'isVerified' => $this->user->isVerified,
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
            'image' => $this->user->image,
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
            'email' => $this->user->email,
            'emailVerified' => $this->user->emailVerified,
            'phone' => $this->user->phoneOne,
            'phoneVerified' => $this->user->phoneVerified,
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
            'image' => $this->user->image,
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
            'image' => $this->user->image,
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
            'created' => $this->user->created,
            'type' => $this->user->image,
        ];
    }
}
