<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer) 
    {
        $this->mailer = $mailer;
    }
    public function sendEmail(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {
        // On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("registration/$template.html.twig")
            ->context($context);

        // On envoie le mail
        $this->mailer->send($email);
    }
}