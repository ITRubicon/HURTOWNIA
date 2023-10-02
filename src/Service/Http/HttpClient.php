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
        
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl . $path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $this->setAuthHeaders();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpHeader);

        // Dodanie opcji --insecure
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $this->fetchedData = curl_exec($this->ch);
        $this->httpCode = (int) curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        curl_close($this->ch);

        $this->timer->stop();
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
}
