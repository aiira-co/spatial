<?php

namespace Core\Application\Logics\Identity\ResetPassword\Commands;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\ResetPasswordRequest;
use Core\Domain\Identity\Signature;
use Spatial\Notify\NotifyGateway;
use Spatial\Psr7\Request;

class UpdateResetRequest extends Request
{
    use IdentityTrait;

    public int $id;
    public object $data;
    private int $signatureType = 66; // 66 - password, 67 - pin, 68 - fingerprint, 69 -faceId

    private ?ResetPasswordRequest $passwordRequest = null;

    /**
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function requestExists(): bool
    {
        $this->getEntityManager();
        $this->passwordRequest = $this->emIdentity->find(ResetPasswordRequest::class, $this->id);

        return $this->passwordRequest !== null;
    }

    /*
     * @return bool
     */
    public function isOldPassword(): bool
    {
        return $this->passwordRequest->person->signatures->last()->authenticate($this->data->password);
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function updatePasswordRequest(): bool
    {
        $this->passwordRequest->resetIp = $this->data->resetIp;
        $this->passwordRequest->isResetDone = true;
        $this->passwordRequest->modified = new \DateTime('now');

        $this->resetPersonPassword();

        $this->emIdentity->persist($this->passwordRequest);
        $this->emIdentity->flush();
        $this->emIdentity->close();
        


//        send mail that password has been changed
        $this->sendEmail();
        return true;
    }

    /**
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    private function resetPersonPassword(): void
    {
        $signature = new Signature();
        $signature->person = $this->passwordRequest->person;
        $signature->setHashed($this->data->password);
        $signature->type = $this->emIdentity->find(Groups::class, $this->signatureType);
        $signature->created = new \DateTime('now');

        $this->emIdentity->persist($signature);
    }


    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendEmail(): void
    {
        $description = "Hi {$this->passwordRequest->person->username},
 <p style='padding:0 16px'>
Your password has been changed.
</p>
";
        (new NotifyGateway())->queueRawEmail(
            commandId: sprintf('spatial-reset-updated-%d', $this->passwordRequest->id),
            recipientEmail: $this->data->email,
            subject: 'Password Updated',
            htmlBody: $description,
            referenceType: 'identity_user',
            referenceId: (string) $this->passwordRequest->person->id,
        );
    }
}