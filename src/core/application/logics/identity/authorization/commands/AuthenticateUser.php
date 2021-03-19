<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Authorization\Commands;

use Core\Domain\Identity\Person;
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class AuthenticateUser extends Request
{
    public $data = [];
    public $params;
    private $_user;

    private $_lockEndMinutes = 5;

    /**
     * Get related User/ User exists?
     *
     * @return boolean
     */
    public function getUserByEmail(): bool
    {
        $this->emIdentity = (new IdentityDB)->emIdentity;

        $user = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t')
            ->where('t.username = :name')
            ->orWhere('t.email = :name')
            ->setParameter('name', $this->data->email)
            ->getQuery()
            ->getSingleResult();


        if (!$user) {
            return false;
        }

        $this->_user = $user;

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * Authenticate found user
     *
     * @return boolean
     */
    public function authUser(): bool
    {
        if ($this->_user->authenticate($this->data->password)) {
            // zero out access fail count
            if ($this->_user->getAccessFailedCount() !== 0) {
                $user = $this->emIdentity->find(Person::class, (int) $this->_user->getId());
                $user->setAccessFailedCount(0);
                $this->emIdentity->flush();
            }
            return true;
        }

        return false;
    }

    /**
     * Get Logged User Basic Info (array)
     *
     * @return array
     */
    public function getVerifiedUser(): array
    {
        return [
            'id' => $this->_user->getId(),
            'image' => $this->_user->getImage(),
            'username' => $this->_user->getUsername(),
            'email' => $this->_user->getEmail(),
            'name' => $this->_user->getName(),
            'cover' => $this->_user->getCover(), // for social
            'tagline' => $this->_user->getTagline(), // for social
        ];
    }

    /**
     * Get Original User Object
     *
     * @return Person
     */
    public function getUser(): Person
    {
        return $this->_user;
    }

    /**
     * Increate Access Fail Count
     *
     * @return boolean
     */
    public function upateAccessFailCount(bool $allow = false): bool
    {
        $user = $this->emIdentity->find(Person::class, (int) $this->_user->getId());

        $count = (int) $this->_user->getAccessFailedCount() + 1;

        $allow ? '' : $user->setAccessFailedCount($count);

        // update db
        if ($count === 5) {
            // enable lockout
            $user->setLockoutEnabled(true);
            // add 5minutes
            $time = new \DateTime('now');
            $time->modify("+{$this->_lockEndMinutes} minutes");
            $user->setLockoutEnd($time);

            // also lockdown for 10 coun for 24hours
        } elseif ($count === 10) {
            // enable lockout
            $user->setLockoutEnabled(true);
            // add 5minutes
            $time = new \DateTime('now');
            $time->modify('+1 day');
            $user->setLockoutEnd($time);
        } elseif ($count === 15) {
            // enable lockout
            $user->setLockoutEnabled(true);
            // add 5minutes
            $time = new \DateTime('now');
            $time->modify('+1 day');
            $user->setLockoutEnd($time);

            // deactivate account
            $user->setActivated(false);
        } else {
            $user->setLockoutEnabled(false);
        }

        $this->emIdentity->flush();
        return true;
    }

    /**
     * Check if account is active
     *
     * @return array
     */
    public function isUserActivated(): array
    {
        return [
            'activated' => $this->_user->getActivated(),
            'lockoutEnabled' => $this->_user->getLockoutEnabled(),
            'lockoutEnd' => $this->_user->getLockoutEnd(),
        ];
    }
}
