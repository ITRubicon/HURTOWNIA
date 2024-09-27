<?php

namespace App\Repository;

use App\Entity\SourceAuth;
use App\Service\TaskReporter\TaskReporter;
use App\Utilities\DateFormatter;
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

    public function __construct(Connection $conn, TaskReporter $reporter)
    {
        $this->db = $conn;
        $this->timer = new Timer();
        $this->reporter = $reporter;
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
            $this->fetchResult = array_chunk($this->fetchResult, 100);
            foreach ($this->fetchResult as $batch) {
                $data = $this->prepareDataToInsert($batch);
                $insFields = $this->makeQueryFields();

                $q = "INSERT INTO $this->table ($insFields) VALUES " . $data['questionMarks'];
                try {
                    $this->db->executeQuery($q, $data['valuesIns'], $data['types']);
                    $this->db->close();
                } catch (\Throwable $th) {
                    $this->reporter->sendErrorReport('DB', 'DMS: ' . $this->source->getName() . PHP_EOL . $th->getMessage(), $th->getCode());
                    throw new \Exception('DMS: ' . $this->source->getName() . PHP_EOL . $th->getMessage(), $th->getCode(), $th);
                }
            }
        }
        $this->fetchResult = [];
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

    protected function prepareDataToInsert(array &$batch): array
    {
        $valuesIns = [];
        $questionMarks = [];
        $fieldsParams = $this->getFieldsParams();
        echo "\nPrzygotowuję dane do zapisu";
        $this->timer->start();

        foreach ($batch as &$row) {
            $row['source'] = $this->source->getName();

            foreach ($fieldsParams as $fp) {
                $val = isset($row[$fp['sourceField']]) ? $row[$fp['sourceField']] : null;

                if (!empty($fp['format']))
                    $val = isset($val) ? $this->formatValue($val, $fp['format']) : null;

                $valuesIns[] = $val;
            }

            unset($row);
            $questionMarks[] = $this->makeQueryPlaceholders(count($fieldsParams));

        }
        echo "\nZużycie pamięci\t\t" . number_format(memory_get_usage() / (1024 * 1024), 2) . 'MB';
        echo "\nMaksymalne zużycie pamięci\t" . number_format(memory_get_peak_usage() / (1024 * 1024), 2) . 'MB';
        echo "\nCzas przygotowania danych:\t " . $this->timer->getInterval() . "s";

        return [
            'valuesIns' => $valuesIns,
            'types' => array_column($fieldsParams, 'type'),
            'questionMarks' => implode(',', $questionMarks),
        ];
    }

    protected function formatValue($value, array $formatOptions)
    {
        try {
            $key = array_keys($formatOptions)[0];
            $format = array_values($formatOptions)[0];

            switch ($key) {
                case 'date':
                    $value = DateFormatter::formatDate($value, $format);
                    break;
                case 'json':
                    $value = DateFormatter::formatJson($value);
                    break;
                case 'int':
                    $value = DateFormatter::formatInt($value);
                    break;
                case 'dec':
                    $value = DateFormatter::formatFloat($value);
                    break;
                default:
                    $value = preg_replace('/[[:cntrl:]]/', '', (string) $value);
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
