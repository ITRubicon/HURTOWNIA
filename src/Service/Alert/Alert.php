<?php

namespace App\Service\Alert;

use App\Repository\EntityWarehouse\JobHistoryRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Alert
{
    private $mailer;

    public function __construct(MailerInterface $mailer, /* \Twig\Environment $templating JobHistoryRepository $jobRepo*/)
    {
        $this->mailer = $mailer;
        // $this->templating = $templating;
        // $this->jobRepo = $jobRepo;
    }


    public function sendCommandAlert(string $msg): void
    {
        $email = (new TemplatedEmail())
            ->from('alert@example.com')
            ->to('andrzej.guzowski@rubicon.katowice.pl')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('ALERT - HURTOWNIA - ' . $_ENV['COMPANY'])
            ->htmlTemplate('emails/alert.html.twig')
            ->context([
                'msg' => $msg,
                'company' => $_ENV['COMPANY']
            ])
        ;

        $this->mailer->send($email);
    }
}
