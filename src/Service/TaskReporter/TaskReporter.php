<?php

namespace App\Service\TaskReporter;

use App\Entity\ApiFetchError;
use App\EntityWarehouse\JobHistory;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\EntityWarehouse\JobHistoryRepository;
use App\Service\Alert\Alert;

class TaskReporter
{
    private $errorRepo;
    private $jobHistory;
    private $alert;

    public function __construct(ApiFetchErrorRepository $errorRepo, JobHistoryRepository $jobHistory, Alert $alert)
    {
        $this->errorRepo = $errorRepo;
        $this->jobHistory = $jobHistory;
        $this->alert = $alert;
    }

    public function reportApiFetchError(string $connectionName, string $path, int $httpCode)
    {
        $error = new ApiFetchError;
        $error
            ->setEndpoint($path)
            ->setSource($connectionName)
            ->setHttpCode($httpCode);
        $this->errorRepo->save($error, true);
    }

    public function setStart($command, $params)
    {
        $history = new JobHistory;
        $history->setCommand($command)
                ->setParameter(json_encode($params));

        return $this->jobHistory->save($history, true);
    }

    public function setError(int $id, string $msg)
    {
        $this->jobHistory->setError($id, $msg);
        $this->sendErrorReport($msg);
    }

    public function setEnd(int $id)
    {
        return $this->jobHistory->setEnd($id);
    }

    public function sendErrorReport($msg)
    {
        // pobrać błędne endpointy
        // pobrać niewykonane zadania
        // wygenerować widok
        // wysłać
        $this->alert->sendCommandAlert($msg);
    }
}