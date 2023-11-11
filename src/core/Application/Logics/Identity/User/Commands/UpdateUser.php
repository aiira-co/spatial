<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Commands;

use Core\Application\Traits\IdentityTrait;
use Core\Application\Enums\GenderEnum;
use Core\Domain\Identity\{Person};
use Infrastructure\Identity\IdentityDB;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class UpdateUser extends Request
{
    use IdentityTrait;

    public object $data;
    public int $id;

    private ?Person $_user;

    /**
     * Check if User exists
     *
     * @return boolean
     */
    public function userExist(): bool
    {
        $this->getEntityManager();
        $this->emIdentity = (new IdentityDB)->emIdentity;
        $this->_user = $this->emIdentity->find(Person::class, $this->id);

        return $this->_user !== null;
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
        $this->_user->image = $this->data->image;
        $this->_user->cover = $this->data->cover;
        $this->_user->surname = $this->data->surname;
        $this->_user->othername = $this->data->othername;
        $this->_user->birthdate = $this->data->birthdate;

        $this->_user->gender = GenderEnum::from((int)$this->data->gender);


        $this->emIdentity->flush();

        return true;

        // return $this->emIdentity->getRepository(Identity::class);
    }


    /**
     * Get Logged User Basic Info (array)
     *
     * @return array
     */
    public function getPersonInfo(): array
    {
        return [
            'id' => $this->_user->id,
            'username' => $this->_user->username,
            'image' => $this->_user->image,
            'email' => $this->_user->email,
            'name' => $this->_user->surname . ' ' . $this->_user->othername,
            'cover' => $this->_user->cover, // for social
        ];
    }

    /**
     * Update User Avatar
     *
     * @return boolean|null
     */
    public function updateUserAvatar(): ?bool
    {
        $this->_user->image = $this->data->image;
        $this->emIdentity->flush();

        return true;
    }

    /**
     * Update user cover image
     *
     * @return boolean|null
     */
    public function updateUserCover(): ?bool
    {

        $this->_user->cover = $this->data->cover;

        $this->emIdentity->flush();

        return true;

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


}
