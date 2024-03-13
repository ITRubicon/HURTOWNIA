<?php

namespace App\Service\Alert;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Alert
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendCommandAlert(string $command, string $msg): void
    {
        $email = (new TemplatedEmail())
            ->from('alert@rubicon.katowice.pl')
            ->to('alert@rubicon.katowice.pl')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('ALERT - HURTOWNIA - ' . $_ENV['COMPANY'])
            ->htmlTemplate('emails/alert.html.twig')
            ->context([
                'cmd' => $command,
                'msg' => $msg,
                'company' => $_ENV['COMPANY']
            ])
        ;

        $this->mailer->send($email);
    }

    public function sendFetchErrors(array $errors): void
    {
        $email = (new TemplatedEmail())
            ->from('alert@example.com')
            ->to('andrzej.guzowski@rubicon.katowice.pl')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('ALERT - HURTOWNIA - ' . $_ENV['COMPANY'] . '. Problematyczne endpointy')
            ->htmlTemplate('emails/fetch_alert.html.twig')
            ->context([
                'errors' => $errors,
                'company' => $_ENV['COMPANY']
            ])
        ;

        $this->mailer->send($email);
    }
}
