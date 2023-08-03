<?php

namespace App\Repository;

use App\Entity\SourceAuth;
use App\Service\TaskReporter\TaskReporter;
use App\Utilities\DataFormatter;
use App\Utilities\DateValidator;
use App\Utilities\Timer;
use Doctrine\DBAL\Connection;

abstract class IBaseRepository
{
    protected $fetchResult = [];
    protected Connection $db;
    protected SourceAuth $source;
    protected $dateFrom;
    protected $dateTo;
    protected TaskReporter $reporter;
    protected $table;
    protected $timer;

    protected abstract function getFieldsParams(): array;
    // protected abstract function fetch(): array;

    public function __construct(Connection $conn)
    {
        $this->db = $conn;
        $this->timer = new Timer();
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

    public function setSource(SourceAuth $source)
    {
        $this->source = $source;
    }

    public function save()
    {
        if (!empty($this->fetchResult)) {
            $data = $this->prepareDataToInsert();
            $insFields = $this->makeQueryFields();

            $q = "INSERT INTO $this->table ($insFields) VALUES " . $data['questionMarks'];
            try {
                $this->db->executeQuery($q, $data['valuesIns'], $data['types']);
                $this->db->close();
            } catch (\Throwable $th) {
                throw new \Exception($th->getMessage(), $th->getCode(), $th);
            }
        }
    }

    public function clearTable()
    {
        dump('Czyszczę tabelę: ' . $this->table);
        $this->db->executeQuery("TRUNCATE TABLE $this->table");
    }

    protected function makeQueryPlaceholders($count = 0)
    {
        $result = array();
        for ($i = 0; $i < $count; $i++) {
            $result[] = '?';
        }

        return '(' . implode(',', $result) . ')';
    }

    protected function makeQueryFields()
    {
        $fields = array_keys($this->getFieldsParams());
        return implode(',', $fields);
    }

    protected function prepareDataToInsert(): array
    {
        $valuesIns = [];
        $questionMarks = [];
        $apiKeys = array_column($this->getFieldsParams(), 'sourceField');
        dump('Przygotowuję dane do zapisu');
        $this->timer->start();
        
        foreach ($this->fetchResult as &$row) {
            // $start = microtime(true);
            $val = [];
            $row['source'] = $this->source->getName();
            
            foreach ($apiKeys as $apiKey) {
                $val = isset($row[$apiKey]) ? $row[$apiKey] : null;

                if (!empty($params['format']))
                    $val = isset($val) ? $this->formatValue($val, $params['format']) : null;
                // else
                //     $val =  $this->formatValue($val, ['whitespaces' => true]); // powiniek załatwić klient http
                $valuesIns[] = $val;
            }

            unset($row);
            $questionMarks[] = $this->makeQueryPlaceholders(count($apiKeys));
            
            // dump(number_format(memory_get_usage() / (1024 * 1024), 4) . 'MB');
            // dump("Czas dla pojedynczego rekordu: " . number_format(microtime(true) - $start, 4) . "s");
        }
        dump('Czas przygotowania danych: ' . $this->timer->getInterval() . "s");

        return [
            'valuesIns' => $valuesIns,
            'types' => $this->getCombinedArray('type'),
            'questionMarks' => implode(',', $questionMarks),
        ];
    }

    protected function getCombinedArray(string $column)
    {
        $fields = $this->getFieldsParams();
        return array_combine(array_keys($fields), array_column($fields, $column));
    }

    protected function formatValue($value, array $formatOptions)
    {
        try {
            $key = array_keys($formatOptions)[0];
            $format = array_values($formatOptions)[0];

            switch ($key) {
                case 'date':
                    $value = DataFormatter::formatDate($value, $format);
                    break;
                case 'json':
                    $value = DataFormatter::formatJson($value);
                    break;
                case 'int':
                    $value = DataFormatter::formatInt($value);
                    break;
                case 'dec':
                    $value = DataFormatter::formatFloat($value);
                    break;
                default:
                    $value = preg_replace('/[[:cntrl:]]/', '', (String) $value);
                    break;
            }
            return $value;
        } catch (\Throwable $th) {
            throw new \Exception('Wystąpił błąd podczas konwertowania wartości. ' . $th->getMessage(), 500, $th);
        }
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
    }
}
