<?php

namespace App\Repository;

use App\Service\Http\HttpClient;
use App\Service\TaskReporter\TaskReporter;
use Doctrine\DBAL\Connection;

abstract class IApiRepository extends IBaseRepository
{
    protected HttpClient $httpClient;

    public function __construct(Connection $conn, HttpClient $client, TaskReporter $reporter)
    {
        parent::__construct($conn);
        $this->httpClient = $client;
        $this->reporter = $reporter;
    }

    protected function fetchApiResult(string $path): array
    {
        echo "\nOdpytywany endopoint:  " . $this->source->getBaseUrl() . $path;
        $this->httpClient->request($this->source, $path);
        
        if ($this->httpClient->getHttpCode() === 200)
            return $this->decodeResponse();
        else {
            if (!$this->isResponseValid()) {
                $this->reporter->reportApiFetchError(
                    $this->source->getName(),
                    $path,
                    $this->httpClient->getHttpCode()
                );
                return [];
                // throw new HttpException(0, 'Nie udało się pobrać danych. Kod http: ' . $this->httpClient->getHttpCode());
            }
            else {
                echo "\nPUSTO!";
                return [];
            }
        }
    }

    protected function isResponseValid()
    {
        $resp = $this->decodeResponse();
        
        if (empty($resp) || $this->httpClient->getHttpCode() === 500 || !preg_match('/NotFound/i', $resp['code']))
            return false;
        else
            return true;
    }

    protected function decodeResponse(): array
    {
        return json_decode($this->httpClient->getContent(), true);
    }
}
