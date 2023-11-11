<?php

namespace Core\Application\Logics\Identity\Authorization\Commands;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
//use Core\Domain\Pixbay\PersonDevice;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Spatial\Psr7\Request;

class SignOut extends Request
{
    use IdentityTrait;

    public object $data;
    public int $id;
    private ?Person $user;
//    private ?PersonDevice $personDevice;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function userExists(): bool
    {
        $this->getEntityManager();
        $this->user = $this->emIdentity->getRepository(Person::class)->find($this->id);
        return $this->user !== null;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function signUserOut(): bool
    {
        $personDeviceCount = $this->user->personDevices->count();
        if ($personDeviceCount > 0) {
            $deviceExists = $this->personDeviceExists();
            if ($deviceExists) {
//                update status if its loggedout
                $deviceStatus = $this->personDevice->status->id;
                if ($deviceStatus === 3) {
                    return true;
                }
                if ($deviceStatus !== $this->data->device->status->id) {
                    $this->updatePersonDeviceStatus();
                }

                return $this->personDevice->activated;
            }
        }
        return false;
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

}