<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\Authorization\Commands;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
use Core\Domain\Identity\Signature;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class AuthenticateUser extends Request
{
    use IdentityTrait;

    public object $data;
    private ?Person $user;

    private int $lockEndMinutes = 5;

    private bool $requestConformation = false;


    /**
     * @return bool
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function getUserByEmail(): bool
    {
        $this->getEntityManager();

        $this->user = $this->emIdentity
            ->getRepository(Person::class)
            ->createQueryBuilder('t')
            ->where('lower(t.username) = lower(:name)')
            ->orWhere('lower(t.email) = lower(:name)')
            ->setParameter('name', $this->data->email)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->user !== null;
        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * Authenticate found user
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function authUser(): bool
    {
        // get current user signature
        $signature = $this->emIdentity
            ->getRepository(Signature::class)
            ->findOneBy(
                [
                    'person' => $this->user->id,
                    'type' => $this->data->signatureType->id ?? 1, // password
                ],
                ['id' => 'DESC']
            );

        if (!$signature) {
            return false;
        }

        if ($signature->authenticate($this->data->password)) {
            // zero out access fail count
            if ($this->user->accessFailedCount !== 0) {
                $user = $this->emIdentity->find(Person::class, $this->user->id);
                if ($user !== null) {
                    $user->accessFailedCount = 0;
                }
                $this->emIdentity->flush();
            }
            $this->emIdentity->close();
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
            'id' => $this->user->id,
            'image' => $this->user->image,
            'username' => $this->user->username,
            'email' => $this->user->email,
            'name' => $this->user->othername . ' ' . $this->user->surname,
            'cover' => $this->user->cover, // for social
            'tagline' => $this->user->tagline, // for social
            'isVerified' => $this->user->isVerified,
            'requestConfirmation' => $this->requestConformation,
        ];
    }

    /**
     * Get Original User Object
     *
     * @return Person
     */
    public function getUser(): Person
    {
        return $this->user;
    }

    /**
     * Increase Access Fail Count
     *
     * @param bool $allow
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function updateAccessFailCount(bool $allow = false): bool
    {
        $user = $this->emIdentity->find(Person::class, $this->user->id);

        $count = $this->user->accessFailedCount + 1;

        if (!$allow && $user !== null) {
            $user->accessFailedCount = $count;
        }

        // update db
        if ($count === 5) {
            // enable lockout
            $user->lockoutEnabled = true;
            // add 5minutes
            $time = new DateTime('now');
            $time->modify("+{$this->lockEndMinutes} minutes");
            $user->lockoutEnd = $time;
            // also lockDown for 10 count for 24hours
        } elseif ($count === 10) {
            // enable lockout
            $user->lockoutEnabled = true;
            // add 5minutes
            $time = new DateTime('now');
            $time->modify('+1 day');
            $user->lockoutEnd = $time;
        } elseif ($count === 15) {
            // enable lockout
            $user->lockoutEnabled = true;
            // add 5minutes
            $time = new DateTime('now');
            $time->modify('+1 day');
            $user->lockoutEnd = $time;

            // deactivate account
            $user->activated = false;
        } else {
            $user->lockoutEnabled = false;
        }

        $this->emIdentity->flush();
        $this->emIdentity->close();
        return true;
    }

    /**
     * @return array
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function isUserActivated(): array
    {
        return [
            'activated' => $this->user->activated,
            'lockoutEnabled' => $this->user->lockoutEnabled,
            'lockoutEnd' => $this->user->lockoutEnd,
//            'deviceAccess' => $this->authPersonDevice()
        ];
    }


//    peron device

    /**
     * @return bool
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function authPersonDevice(): bool
    {
        $personDeviceCount = $this->user->personDevices->count();
        if ($personDeviceCount > 0) {
            $deviceExists = $this->personDeviceExists();
            if ($deviceExists) {
//                update status if its loggedout
                $deviceStatus = $this->personDevice->status->id;
                if ($deviceStatus === 3) {
                    return false;
                }
                if ($deviceStatus !== $this->data->device->status->id) {
                    $this->updatePersonDeviceStatus();
                }

                return $this->personDevice->activated;
            }


            return $this->registerAnotherPersonDevice();
        }

        return $this->registerNewPersonDevice(true);
    }

    /**
     * @return bool
     * @throws NonUniqueResultException
     */
    private function personDeviceExists(): bool
    {
        $this->personDevice = $this->emIdentity
            ->getRepository(PersonDevice::class)
            ->createQueryBuilder('t')
            ->where('t.person = :person')
            ->andWhere('t.deviceId = :deviceId')
            ->andWhere('t.app = :app')
            ->setParameters(
                [
                    'deviceId' => $this->data->device->deviceId,
                    'app' => $this->data->device->app->id,
                    'person' => $this->user->id,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();

        return $this->personDevice !== null;
    }


    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Doctrine\ORM\ORMException
     */
    private function registerAnotherPersonDevice(): bool
    {
        $allowDevice = false;
//        check if user has two-way auth enabled
        if ($this->user->twoWayAuth) {
            $this->requestConformation = true;
//            send a message or mail to verify
        } else {
            $allowDevice = true;
        }
        return $this->registerNewPersonDevice($allowDevice);
    }


    /**
     * @param bool $activated
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function registerNewPersonDevice(bool $activated = false): bool
    {
        $app = $this->emIdentity->find(Groups::class, $this->data->device->app->id);
        $deviceType = $this->emIdentity->find(Groups::class, $this->data->device->deviceType->id);
        $status = $this->emIdentity->find(Groups::class, $this->data->device->status->id);

        $this->personDevice = new PersonDevice();
        $this->personDevice->person = $this->user;
        $this->personDevice->app = $app;
        $this->personDevice->activated = $activated;
        $this->personDevice->os = $this->data->device->os;
        $this->personDevice->osVersion = $this->data->device->osVersion;
        $this->personDevice->browser = $this->data->device->browser;
        $this->personDevice->browserVersion = $this->data->device->browserVersion;
        $this->personDevice->device = $this->data->device->device;
        $this->personDevice->deviceType = $deviceType;
        $this->personDevice->deviceId = $this->data->device->deviceId;
        $this->personDevice->status = $status;
        $this->personDevice->created = new DateTime('now');

        $this->emIdentity->persist($this->personDevice);
        $this->emIdentity->flush();

        return true;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function updatePersonDeviceStatus(): void
    {
        $status = $this->emIdentity->find(Groups::class, $this->data->device->status->id);
        $this->personDevice->status = $status;
        $this->emIdentity->persist($this->personDevice);
        $this->emIdentity->flush();
    }
}
