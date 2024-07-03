<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FkCechyRepository extends IApiRepository
{
    private string $endpoint = '/api/accounting/v3/01/dta/row-data?tid=FK.CECHY';
    protected $table = 'tema_fk_cechy';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->removeOldData();

        $resCount = 0;
        $res = [];
        do {
            $nextTimestamp = '';            
            if (!empty($res['lastTimestamp']))
                $nextTimestamp = '&ts=' . urlencode($res['lastTimestamp']);

            $date = '&dt=' . date('Y-m-d');
            if (!empty($this->dateFrom))
                $date = '&dt=' . $this->dateFrom;

            $res = $this->fetchApiResult($this->endpoint . $nextTimestamp . $date);
            if (empty($res))
                continue;
            
            $this->fetchResult = array_merge($this->fetchResult, $res['rows']);
            $resCount += count($res['rows']);
        } while ($res['fetchNext']);
        $this->addYear();
        $this->save();

        return ['fetched' => $resCount];
    }

    private function addYear()
    {
        $this->fetchResult = array_map(function($item) {
            $item['rok'] = date('Y', strtotime($this->dateFrom));
            return $item;
        }, $this->fetchResult);
    }

    protected function getFieldsParams(): array
    {
        return [
            'wpis_id' => ['sourceField' => 'ID_WPISU', 'type' => ParameterType::STRING],
            'kod_cechy' => ['sourceField' => 'KODCECHY', 'type' => ParameterType::STRING],
            'wartosc' => ['sourceField' => 'WARTOSC', 'type' => ParameterType::STRING],
            'recno' => ['sourceField' => 'RECNO', 'type' => ParameterType::INTEGER],
            'timestamp' => ['sourceField' => 'TIMESTAMP__', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
            'rok' => ['sourceField' => 'rok', 'type' => ParameterType::INTEGER],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
    }

    private function removeOldData()
    {
        $year = date('Y', strtotime($this->dateFrom));
        $source = $this->source->getName();
        $this->db->executeQuery("DELETE FROM $this->table WHERE source = '$source' AND rok = $year");
    }

    public function clearTable() {}
}
