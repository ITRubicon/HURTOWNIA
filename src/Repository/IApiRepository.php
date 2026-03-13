<?php

namespace App\Repository;

use App\Service\Http\HttpClient;
use App\Service\TaskReporter\TaskReporter;
use Doctrine\DBAL\Connection;

abstract class IApiRepository extends IBaseRepository
{
    protected HttpClient $httpClient;
    protected $fetchLimit = 200;

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

        // bypass na dzikie rozwiązania TEMY - niektóre endpointy zwracają 400/404, mimo że zapytanie jest poprawne, ale u nich nie ma danych, więc zwracają błąd, zamiast pustej tablicy
        if (!preg_match('/NotFound|unknownTable/i', $this->httpClient->getContent()))
            return false;
        
        return true;
    }

    protected function decodeResponse(): array
    {
        $response = $this->httpClient->getContent();

        return json_decode($response, true);
    }
}
