<?php

namespace App\Service\Alert;

use App\Repository\EntityWarehouse\JobHistoryRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Alert
{
    private $mailer;
    private $templating;
    private $con;
    private $jobRepo;
    private $userRepo;

    public function __construct(MailerInterface $mailer, /* \Twig\Environment $templating */ JobHistoryRepository $jobRepo)
    {
        $this->mailer = $mailer;
        // $this->templating = $templating;
        $this->jobRepo = $jobRepo;
    }


    public function sendCommandAlert(string $msg): void
    {
        $email = (new Email())
            ->from('alert@example.com')
            ->to('andrzej.guzowski@rubicon.katowice.pl')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('ALERT - HURTOWNIA')
            ->html('<p>See Twig integration for better HTML integration!</p><br>' . $msg);

        $this->mailer->send($email);
    }
}
