<?php

namespace App\Repository;

use App\Service\Http\HttpClient;
use App\Service\TaskReporter\TaskReporter;
use Doctrine\DBAL\Connection;

abstract class IApiRepository extends IBaseRepository
{
    protected HttpClient $httpClient;
    protecte $fetchLimit = 200;

    public function __construct(Connection $conn, HttpClient $client, TaskReporter $reporter)
    {
        parent::__construct($conn, $reporter);
        $this->httpClient = $client;
    }

    protected function fetchApiResult(string $path): array
    {
        echo "\nOdpytywany endopoint:  " . $this->source->getBaseUrl() . $path;
        $this->httpClient->request($this->source, $path);
        echo "\nCzas zapytania: " . $this->httpClient->getRequestTime() . "s";
        
        if ($this->httpClient->getHttpCode() === 200)
            return $this->decodeResponse();
        else {
            if (!$this->isResponseValid()) {
                $this->reporter->reportApiFetchError(
                    $this->source->getName(),
                    $path,
                    $this->httpClient->getHttpCode()
                );
            }
            return [];
        }
    }

    protected function isResponseValid()
    {
        if ($this->httpClient->getHttpCode() === 500)
            return false;
        if (empty($this->httpClient->getContent()))
            return false;
        if (!preg_match('/NotFound/i', $this->decodeResponse()['code']))
            return false;
        
        return true;
    }

    protected function decodeResponse(): array
    {
        return json_decode($this->httpClient->getContent(), true);
    }
}
