<?php

namespace App\Service\Http;

use App\Entity\IConnection;
use App\Utilities\Timer;

class HttpClient
{
    private $auth;
    private $authType;
    private $baseUrl;
    private $ch;
    private $fetchedData;
    private $httpCode;
    private $httpHeader = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    private $timer;
    private const MAX_ATTEMPTS = 3;
    private const WAIT_TIME_STEP = 10; // seconds
    private $tryColors = [
        "\033[0;32m", // green
        "\033[0;33m", // yellow
        "\033[0;31m", // red
    ];
    

    public function __construct()
    {
        $this->timer = new Timer;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getContent()
    {
        $this->removeUnprintableChars();
        return $this->fetchedData;
    }

    public function request(IConnection $apiConn, $path)
    {
        $this->setParams($apiConn);
        $this->timer->start();
        $attempt = 1;
        $success = false;

        do {
            echo PHP_EOL . $this->tryColors[$attempt - 1] . "Próba $attempt" . "\033[0m" . PHP_EOL;
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl . $path);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            $this->setAuthHeaders();
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpHeader);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 300);

            $this->fetchedData = curl_exec($this->ch);
            if ($this->fetchedData === false) {
                if (curl_errno($this->ch) == CURLE_OPERATION_TIMEDOUT) {
                    $attempt++;
                    if ($attempt >= 3) {
                        $this->timer->stop();
                        $pingResult = $this->ping($apiConn);
                        throw new \Exception("Przekroczono czas oczekiwania na odpowiedź serwera po 3 próbach. " . $pingResult, 1);
                    }
                } else {
                    $this->timer->stop();
                    throw new \Exception("Błąd podczas pobierania danych: " . curl_error($this->ch), 1);
                }

                sleep(self::WAIT_TIME_STEP * $attempt);
            } else
                $success = true;

            $this->httpCode = (int) curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            curl_close($this->ch);
        } while ($attempt <= self::MAX_ATTEMPTS && !$success);

        if ($success)
            $this->timer->stop();
    }

    public function requestMulti(IConnection $apiConn, array $paths): array
    {
        $this->setParams($apiConn);
        $results = [];
        $attempts = [];
        $maxAttempts = self::MAX_ATTEMPTS;

        // Initialize attempts
        foreach ($paths as $key => $path) {
            $attempts[$key] = 1;
        }

        $pending = $paths;

        while (!empty($pending)) {
            $this->timer->start(); // Start timing the batch

            $multiHandle = curl_multi_init();
            $handles = [];

            // Prepare handles for pending requests
            foreach ($pending as $key => $path) {
                echo PHP_EOL . $this->baseUrl . $path;
                echo PHP_EOL . $this->tryColors[$attempts[$key] - 1] . "Próba " . $attempts[$key] . "\033[0m" . PHP_EOL;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $path);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeader);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_multi_add_handle($multiHandle, $ch);
                $handles[$key] = $ch;
            }

            // Execute all requests
            $running = null;
            do {
                curl_multi_exec($multiHandle, $running);
                curl_multi_select($multiHandle);
            } while ($running > 0);

            // Collect results and determine which need retry
            $nextPending = [];
            foreach ($handles as $key => $ch) {
                $content = curl_multi_getcontent($ch);
                $error = curl_errno($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($content === false || $error || $httpCode >= 500 || empty($content)) {
                    if ($attempts[$key] < $maxAttempts) {
                        $attempts[$key]++;
                        $nextPending[$key] = $paths[$key];
                    } else {
                        $results[$key] = $content; // Save whatever we got (could be false)
                    }
                } else {
                    $results[$key] = $content;
                }
                curl_multi_remove_handle($multiHandle, $ch);
                curl_close($ch);
            }
            curl_multi_close($multiHandle);

            $this->timer->stop(); // Stop timing the batch
            echo "\nCzas batcha: " . $this->timer->getInterval() . "s\n";

            if (!empty($nextPending)) {
                sleep(self::WAIT_TIME_STEP); // Wait before retrying
            }
            $pending = $nextPending;
        }

        // Ensure results are in the same order as input
        ksort($results);

        return $results;
    }

    public function getRequestTime()
    {
        return $this->timer->getInterval();
    }

    private function setAuthHeaders()
    {
        switch (strtoupper($this->authType)) {
            case 'BASIC_AUTH':
                curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($this->ch, CURLOPT_USERPWD, "$this->auth");
                break;
            case 'WEBAPIKEY':
                if (!in_array($this->auth, $this->httpHeader))
                    array_push($this->httpHeader, $this->auth);
                break;
            default:
                throw new \Exception("Nieznany lub niepoprawny sposób autoryzacji", 1);
                break;
        }
    }

    private function setParams(IConnection $apiConn)
    {
        $this->auth = $apiConn->getAuth();
        $this->authType = $apiConn->getAuthType();
        $this->baseUrl = $apiConn->getBaseUrl();
    }

    private function removeUnprintableChars()
    {
        $this->fetchedData = preg_replace('/[[:cntrl:]]/', '', $this->fetchedData);
    }

    private function ping(IConnection $apiAuth)
    {
        $regex = '/^(https?:\/\/)([a-zA-Z0-9\.\-]+)(:[0-9]+)?$/';
        $matches = [];
        preg_match($regex, $apiAuth->getBaseUrl(), $matches);
        $host = $matches[2];

        if ($matches[2])    
            return shell_exec("ping -c 3 $host");
        
        return "Nieprawidłowy adres URL: " . $host;
    }
}
