<?php

namespace App\Service\TaskReporter;

use App\Entity\ApiFetchError;
use App\Repository\ApiFetchErrorRepository;

class TaskReporter
{
    private $errorRepo;

    public function __construct(ApiFetchErrorRepository $errorRepo)
    {
        $this->errorRepo = $errorRepo;
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
}