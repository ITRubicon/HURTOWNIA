<?php

namespace App\Service\Alert;

use App\Repository\EntityWarehouse\JobHistoryRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Alert
{
    private $mailer;
    private $templating;
    private $con;
    private $jobRepo;
    private $userRepo;

    public function __construct(/* \Swift_Mailer $mailer, \Twig\Environment $templating,  ContainerInterface $con, */ JobHistoryRepository $jobRepo)
    {
        /* $this->mailer = $mailer;
        $this->templating = $templating;
        $this->con = $con; */
        $this->jobRepo = $jobRepo;
        // $this->userRepo = $userRepo; // nie będzie User Repo. Domyślnie na it ?
    }


    public function sendCommandAlert($alertId, $recipients = FALSE)
    {
        dump($alertId);
        // $mailer = $this->con->get('swiftmailer.mailer.alert');

        // $mail = $this->jobRepo->get($alertId);

        // if (!$mail || count($mail) < 1) {
        //     throw new \Exception('Brak emaila: ' . $alerId);
        // }

        // $message = new \Swift_Message('ALERT MMC - ' . $mail['komenda']);

        // $recipients = $this->userRepo->getEmailByRoles('ROLE_ALERT_SYSTEM');

        // $parametry = json_decode($mail['parametry'], true);

        // $error = $mail['error'];


        // $body = $this->templating->render(
        //     'alerty/systemAlertMail.html.twig',
        //     [
        //         'komenda' =>  $mail['komenda'],
        //         'parametry' =>  $parametry,
        //         'error' =>  $error
        //     ]
        // );



        // $message->setFrom(['mmc.alert@rubicon.katowice.pl' => 'Alerty MMC'])
        //     ->setTo($recipients)
        //     ->setBody($body, 'text/html');


        // $params = [];


        // $mailer->send($message);
    }

    public function sendMapowanieAlert($info, $recipients = FALSE)
    {
        dump($info);
        // $mailer = $this->con->get('swiftmailer.mailer.alert');

        // $message = new \Swift_Message('ALERT MMC - Należnosci mapowanie');

        // $recipients = $this->userRepo->getEmailByRoles('ROLE_ALERT_SYSTEM');

        // $opis = $info['ile'] . " niezmapowanychy pozycji. \n";

        // foreach ($info['wpisy'] as  $value) {
        //     $opis .= "\n";
        //     foreach ($value as  $poz)
        //         $opis .= "-> $poz ";
        // }


        // $body = $this->templating->render('alerty/systemOtherAlertMail.html.twig', [
        //     'opis' =>  $opis
        // ]);



        // $message->setFrom(['mmc.alert@rubicon.katowice.pl' => 'Alerty MMC'])
        //     ->setTo($recipients)
        //     ->setBody($body, 'text/html');


        // $params = [];


        // $mailer->send($message);
    }

    public function sendInfoAlert($title, $msg)
    {
        dump($title, $msg);
        // $mailer = $this->con->get('swiftmailer.mailer.alert');

        // $message = new \Swift_Message($title);

        // $recipients = $this->userRepo->getEmailByRoles('ROLE_ALERT_SYSTEM');

        // $body = $this->templating->render('alerty/systemOtherAlertMail.html.twig', [
        //     'opis' =>  $msg,
        // ]);

        // // $recipients = ['andrzej.guzowski@rubicon.katowice.pl'];
        // $message->setFrom(['mmc.alert@rubicon.katowice.pl' => 'Alerty MMC'])
        //     ->setTo($recipients)
        //     ->setBody($body, 'text/html');

        // $mailer->send($message);
    }
}
