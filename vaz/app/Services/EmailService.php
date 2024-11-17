<?php
namespace App\Services;

use Nette\Mail\SmtpMailer;
use Nette\Mail\Mailer;

class EmailService
{
    private Mailer $mailer;

    public function __construct($smtpConfig)
    {
        $this->mailer = new SmtpMailer($smtpConfig['host'], $smtpConfig['username'], $smtpConfig['password'], $smtpConfig['port'], $smtpConfig['secure']);
    }

    public function sendEmail(string $from, string $to, string $subject, string $body): void
    {
        $mail = new \Nette\Mail\Message();
        $mail->setFrom($from)
            ->addTo($to)
            ->setSubject($subject)
            ->setHtmlBody($body);

        $this->mailer->send($mail);
    }
}
