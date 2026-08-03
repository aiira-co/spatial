<?php

namespace Core\Application\Logics\Identity\ResetPassword\Commands;

use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\Person;
use Core\Domain\Identity\ResetPasswordRequest;
use Spatial\Notify\NotifyGateway;
use Spatial\Psr7\Request;

class CreateResetRequest extends Request
{
    use IdentityTrait;

    public object $data;

    private ?Person $person = null;

    public function personExists(): bool
    {
        $this->getEntityManager();
        $this->person = $this->emIdentity->getRepository(Person::class)
            ->findOneBy(['email' => $this->data->email]);

        return $this->person !== null;
    }

    public function isAccountActive(): bool
    {
        return $this->person->activated;
    }
    /**
     * @return bool
     * @throws \Exception
     */
    public function createResetRequest(): bool
    {
        $request = new ResetPasswordRequest();
        $request->person = $this->person;
        $request->requestedIp = $this->data->requestedIp;
        $request->token = $this->generateToken();

        $request->isResetDone = false;
        $request->created = new \DateTime('now');
        $request->modified = new \DateTime('now');

        $this->emIdentity->persist($request);
        $this->emIdentity->flush();
        $this->emIdentity->close();
        

        return $this->sendEmail($request);
    }


    /**
     * @throws \Exception
     */
    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * @param \Core\Domain\Pixbay\ResetPasswordRequest $request
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendEmail(ResetPasswordRequest $request): bool
    {
        $description = "Hi {$request->person->username},
 <p style='padding:0 16px'>
 Forgot your password? No worries, we’ve got you covered. Click the link below to reset your password.
</p>

<p style='padding:0 16px'>
<a style='outline:none; padding:8px 16px; border-radius: 16px; background-color: deeppink; color:white; text-decoration:none;' href='https://aiira.co/reset-password/{$request->token}'>Reset Password</a>
</p>

<p style='padding:0 16px'>
<small>*If you didn’t make this request, or made it by mistake, please ignore this email. Your password will remain as it was.</small>
</p>
";
        (new NotifyGateway())->queueRawEmail(
            commandId: sprintf('spatial-reset-create-%d', $request->id),
            recipientEmail: $this->data->email,
            subject: 'Forgot your password? We can help',
            htmlBody: $description,
            referenceType: 'identity_user',
            referenceId: (string) $request->person->id,
        );
        return true;
    }

}