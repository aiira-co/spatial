<?php

declare(strict_types=1);

namespace Core\Application\Logics\Identity\User\Commands;

use Core\Application\Enums\AccountTypeEnum;
use Core\Application\Enums\GenderEnum;
use Core\Application\Enums\SignatureTypeEnum;
use Core\Application\Traits\IdentityTrait;
use Core\Domain\Identity\{Person, Signature};
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Infrastructure\Services\EmailService;
use Spatial\Psr7\Request;

/**
 * Request To be passed to Its' Handler
 * Handler can use this class's properties and methods
 * for Server Request, etc
 */
class CreateUser extends Request
{
    use IdentityTrait;

    public object $data;
    private int $accountType = 41; // Individual Account Type
    private int $genderId = 45;
    private string $lang = 'en'; // Individual Account Type
    private int $signatureType = 1; // 1 - password, 2 - pin, 3 - fingerprint, 4 -faceId


    /**
     * @return int|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws \JsonException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function createUser(): ?int
    {
        $this->getEntityManager();

        $user = new Person();


        //create account phase 1
        $user->tagline = 'I Am New Here';
        $user->bio = 'About Myself';
        $user->username = $this->data->username;
        $user->image = 'avatar/default.jpg';
        $user->email = $this->data->email;
        $user->gender = GenderEnum::from((int)$this->data->gender);
        $user->created = new DateTime('now');
        $user->emailVerified = true;
        $user->phoneVerified = false;
        $user->twoWayAuth = false;
        $user->isVerified = false;
        $user->activated = true;
        $user->lockoutEnabled = false;
        $user->accessFailedCount = 0;
        $user->accountType = AccountTypeEnum::from((int)$this->data->accountType);
        $user->language = $this->lang;
        $user->country = $this->geoPlugin->countryCode ?? 'Ghana';
        $user->city = $this->geoPlugin->city ?? 'Kumasi';
        $user->timezone = 'UTC 0:00';

        $this->emIdentity->persist($user);

//        create set signature
        $signature = new Signature();
//        66 - password, 67 - pin, 68 - fingerprint, 69 -faceId
        $signature->person = $user;
        $signature->setHashed($this->data->password);
        $signature->type = SignatureTypeEnum::from($this->signatureType);
        $signature->created = new DateTime('now');

        $this->emIdentity->persist($signature);

        $this->emIdentity->flush();
        $this->emIdentity->close();

        $this->sendEmail($user);

        return $user->id;
        // return $this->emIdentity->getRepository(Identity::class);
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendEmail($user): void
    {
        $description = "Hi {$user->username},
 <p style='padding:0 16px'>
We wanted to confirm that your new Pixbay account is registered and good to go.
 </p>
 <p style='padding:0 16px'>
 We hope you enjoy your time with us.
 </p>
 <p style='padding:0 16px'>
 If you have any questions, please don't hesitate to contact us at support@aiira.co
</p>
";
        $mail = new EmailService();
        $payload = $mail->from('no_reply@aiira.co', 'Team Aiira')
            ->to([
                (object)[
                    'name' => $user->username,
                    'email' => $user->email
                ]
            ])
            ->send('Welcome to Pixaby. Let’s Get Started!', $description);
    }
}
