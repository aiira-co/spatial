<?php

namespace Core\Application\Logics\Identity\ResetPassword\Queries;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\ResetPasswordRequest;
use Spatial\Psr7\Request;

class GetResetRequest extends Request
{
    use IdentityTrait;

    public string $token;
    private ?ResetPasswordRequest $resetPassword;

    /**
     * @return bool
     */
    public function requestTokenExists(): bool
    {
        $this->getEntityManager();

        $this->resetPassword = $this->emIdentity
            ->getRepository(ResetPasswordRequest::class)
            ->findOneBy(['token' => $this->token]);
        return $this->resetPassword !== null;
    }

    /**
     * @return bool
     */
    public function isTokenActive(): bool
    {
        $this->emIdentity->close();
        
        return !$this->resetPassword->isResetDone || !$this->resetPassword->isExpired();
    }

    /**
     * @return array
     */
    public function getResetPasswordRequest(): array
    {
        $data = [
            'id' => $this->resetPassword->id,
            'person' => [
                'username' => $this->resetPassword->person->username,
                'email' => $this->resetPassword->person->email,
                'image' => $this->resetPassword->person->image,
            ]
        ];

        $this->emIdentity->close();
        return $data;
    }
}