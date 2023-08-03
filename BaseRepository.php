<?php

namespace App\Repository;

use App\Entity\IApiConnection;
use App\Service\Api\HttpClient;
use App\Service\Report\TaskReporter;
use App\Utilities\DateFormater;
use App\Utilities\DateValidator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\ToolsException;
use Exception;

abstract class BaseRepository
{
    protected $db;
    protected IApiConnection $apiConnParams;
    protected HttpClient $httpClient;
    protected $reporter;
    protected $table;
    protected $apiResult = [];
    protected $dateFrom;
    protected $dateTo;

    protected abstract function getApiFields(): array;

    public function __construct(Connection $conn, HttpClient $client, TaskReporter $reporter)
    {
        $this->db = $conn;
        $this->httpClient = $client;
        $this->reporter = $reporter;
    }

    public function setApiConnection(IApiConnection $conn)
    {
        $this->apiConnParams = $conn;
    }

    public function setDateFrom($dateFrom)
    {
        if (DateValidator::isDateCorrect($dateFrom) && DateValidator::isDbDateFormat($dateFrom))
            $this->dateFrom = $dateFrom;
        else
            throw new \Exception("Niepoprawny format daty od. Podałeś: $dateFrom", 99);
    }

    public function setDateTo($dateTo)
    {
        if (DateValidator::isDateCorrect($dateTo) && DateValidator::isDbDateFormat($dateTo))
            $this->dateTo = $dateTo;
        else
            throw new \Exception("Niepoprawny format daty do. Podałeś: $dateTo", 99);
    }

    public function save()
    {
        if (!empty($this->apiResult)) {
            $data = $this->prepareDataToInsert();
            $insFields = $this->makeQueryFields();

            $q = "INSERT INTO $this->table ($insFields) VALUES " . $data['questionMarks'];
            try {
                $this->db->executeQuery($q, $data['valuesIns'], $data['types']);
                $this->db->close();
            } catch (\Throwable $th) {
                throw new Exception($th->getMessage(), $th->getCode(), $th);
            }
        }
    }

    public function clearTable()
    {
        dump('Czyszczę tabelę: ' . $this->table);
        $this->db->executeQuery("TRUNCATE TABLE $this->table");
    }

    protected function makeQueryPlaceholders($count = 0, $text = '?', $separator = ",")
    {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

    protected function makeQueryFields()
    {
        $fields = array_keys($this->getApiFields());
        return implode(',', $fields);
    }

    protected function prepareDataToInsert(): array
    {
        $valuesIns = [];
        $questionMarks = [];
        $types = [];
        $fields = $this->getApiFields();
        $i = 1;
        
        foreach ($this->apiResult as $i => $row) {
            // $start = microtime(true);
            $val = [];
            $row['source'] = $this->apiConnParams->getName();
            
            foreach ($fields as $key => $params) {
                $val[$key] = isset($row[$params['sourceField']]) ? $row[$params['sourceField']] : null;
                $types[$key] = $params['type'];

                if (!empty($params['format']))
                    $val[$key] = isset($val[$key]) ? $this->formatValue($val[$key], $params['format']) : null;
                else
                    $val[$key] =  $this->formatValue($val[$key], ['whitespaces' => true]);

            }

            unset($this->apiResult[$i]);
            $questionMarks[] = '(' . $this->makeQueryPlaceholders(count($val)) . ')';
            $valuesIns = array_merge($valuesIns, array_values($val));
            $i++;
            // $end = microtime(true);
            // dump(number_format(memory_get_usage() / (1024 * 1024), 4) . 'MB');
            // dump("Rekord $i. Czas: " . number_format($end - $start, 4) . "s. Tablica: " . count($this->apiResult));
        }

        return [
            'valuesIns' => $valuesIns,
            'types' => $types,
            'questionMarks' => implode(',', $questionMarks),
        ];
    }

    protected function fetchApiResult(string $path): array
    {
        echo "\nOdpytywany endopoint:  " . $this->apiConnParams->getBaseUrl() . $path;
        $this->httpClient->request($this->apiConnParams, $path);
        
        if ($this->httpClient->getHttpCode() === 200)
            return json_decode($this->httpClient->getContent(), true);
        else {
            $resp = json_decode($this->httpClient->getContent(), true);
            if (empty($resp) || $this->httpClient->getHttpCode() === 500 || !preg_match('/NotFound/i', $resp['code'])) {
                $this->reporter->reportApiFetchError(
                    $this->apiConnParams->getName(),
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

    protected function formatValue($value, array $formatOptions)
    {
        try {
            $key = array_keys($formatOptions)[0];
            $format = array_values($formatOptions)[0];

            switch ($key) {
                case 'date':
                    $value = DateFormater::createFormat($value, $format);
                    break;
                case 'json':
                    $value = json_encode($value);
                    break;
                case 'int':
                    $value = (int) $value;
                    break;
                case 'dec':
                    $value = (float) $value;
                    break;
                default:
                    $value = str_replace(["\r\n", "\r", "\n", "\t"], '', (String) $value);
                    break;
            }

            return $value;
        } catch (\Throwable $th) {
            throw new ToolsException('Wystąpił błąd podczas konwertowania wartości. ' . $th->getMessage(), 500, $th);
        }
    }

    protected function clearDataArrays()
    {
        $this->apiResult = [];
    }
}
