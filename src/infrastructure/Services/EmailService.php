<?php


namespace Infrastructure\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private object $mail;

    public function __construct()
    {
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Mailer = "smtp";
        $mail->Host = getenv('SMTP_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER');
        $mail->Password = getenv('SMTP_PASS');
        $mail->SMTPSecure = getenv('SMTP_SECURE');;
        $mail->Port = getenv('SMTP_PORT');;
        //        $mail->SMTPDebug = 1;

        $this->mail = $mail;
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function from(string $address, string $name): self
    {
        $this->mail->setFrom($address, $name);
        $this->mail->addReplyTo($address, $name);
        return $this;
    }

    /**
     * @param array $address
     * @return $this
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function to(array $address): self
    {
        foreach ($address as $user) {
            $this->mail->addAddress($user->email, $user->name);
        }
        return $this;
    }

    /**
     * @param string $subject
     * @param string $message
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(string $subject, string $message): array
    {
        $this->mail->Subject = $subject;
        $this->mail->isHTML(true);

        $htmlDecoded = htmlspecialchars_decode($message);
        $year = date('Y');


        $mailContent = "<div style=' width:100%; background-color:#f1f1f1; min-width:600px; border-radius: 16px; padding:24px 0'>
    <div style='text-align: center; padding:16px;'>
            <img src='https://aiira.co/assets/images/logo/aiira.png' style='width: 170px; margin-top:32px;' alt='Aiira Inc.'/>
            <br/>
            <br/>
            
            <div style='padding:16px 0; font-family:arial,helvetica,sans-serif;font-size: 18px'>
                {$subject}
            </div>
    </div>
    
    <div style='min-height: 200px; margin:0 auto; width: 80%; max-width: 600px; background-color: white; border-radius: 8px; font-family:arial,helvetica,sans-serif;font-size:14px;color:#202020;line-height:19px;line-height:134%;letter-spacing:.5px'>
        <p style='padding:24px'>{$htmlDecoded}</p>
    </div>
        
     <div style=' min-height: 100px; margin:20px auto; width: 80%; max-width: 500px; font-family:arial,helvetica,sans-serif; font-size:10px; color:#202020; text-align:center; line-height:12px'>

                                            <p >
    © {$year}, Aiira, Inc . All rights reserved . Aiira, Aiira Studios, the Aiira
                                                 logo, airSuite, and the Aiira Store logo are trademarks or registered
                                                trademarks of Aiira, Inc . in the GH and elsewhere . All other
                                                trademarks are the property of their respective owners .

                                            </p >

                                        </div >
</div >
    ";


        $this->mail->MsgHTML($mailContent);

        $sent = false;

        if ($this->mail->send()) {
            $sent = true;
        } else {
            var_dump($this->mail);
        }
        return [
            'success' => $sent,
            'message' => 'Mail Process!'
        ];
    }
}
