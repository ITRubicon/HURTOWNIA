<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FkKontRepository extends IApiRepository
{
    private string $endpoint = '/api/accounting/v3/01/dta/row-data?tid=FK.KONT';
    protected $table = 'tema_fk_kont';
    
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

            if (count($this->fetchResult) >= $this->fetchLimit) {
                $this->addYear();
                $this->save();
                $this->fetchResult = [];
            }
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
            'konto' => ['sourceField' => 'NUMER', 'type' => ParameterType::STRING],
            'nazwa' => ['sourceField' => 'NAZWA', 'type' => ParameterType::STRING],
            'nazwa2' => ['sourceField' => 'NAZWA2', 'type' => ParameterType::STRING],
            'rodzaj' => ['sourceField' => 'RODZAJ', 'type' => ParameterType::STRING],
            'kontrah' => ['sourceField' => 'KONTRAH', 'type' => ParameterType::STRING],
            'kod_kraj' => ['sourceField' => 'KOD_KRAJ', 'type' => ParameterType::STRING],
            'nip' => ['sourceField' => 'NIP', 'type' => ParameterType::STRING],
            'bo_winien' => ['sourceField' => 'BO_WINIEN', 'type' => ParameterType::STRING],
            'bo_ma' => ['sourceField' => 'BO_MA', 'type' => ParameterType::STRING],
            'wn01' => ['sourceField' => 'WN_01', 'type' => ParameterType::STRING],
            'wn02' => ['sourceField' => 'WN_02', 'type' => ParameterType::STRING],
            'wn03' => ['sourceField' => 'WN_03', 'type' => ParameterType::STRING],
            'wn04' => ['sourceField' => 'WN_04', 'type' => ParameterType::STRING],
            'wn05' => ['sourceField' => 'WN_05', 'type' => ParameterType::STRING],
            'wn06' => ['sourceField' => 'WN_06', 'type' => ParameterType::STRING],
            'wn07' => ['sourceField' => 'WN_07', 'type' => ParameterType::STRING],
            'wn08' => ['sourceField' => 'WN_08', 'type' => ParameterType::STRING],
            'wn09' => ['sourceField' => 'WN_09', 'type' => ParameterType::STRING],
            'wn10' => ['sourceField' => 'WN_10', 'type' => ParameterType::STRING],
            'wn11' => ['sourceField' => 'WN_11', 'type' => ParameterType::STRING],
            'wn12' => ['sourceField' => 'WN_12', 'type' => ParameterType::STRING],
            'ma01' => ['sourceField' => 'MA_01', 'type' => ParameterType::STRING],
            'ma02' => ['sourceField' => 'MA_02', 'type' => ParameterType::STRING],
            'ma03' => ['sourceField' => 'MA_03', 'type' => ParameterType::STRING],
            'ma04' => ['sourceField' => 'MA_04', 'type' => ParameterType::STRING],
            'ma05' => ['sourceField' => 'MA_05', 'type' => ParameterType::STRING],
            'ma06' => ['sourceField' => 'MA_06', 'type' => ParameterType::STRING],
            'ma07' => ['sourceField' => 'MA_07', 'type' => ParameterType::STRING],
            'ma08' => ['sourceField' => 'MA_08', 'type' => ParameterType::STRING],
            'ma09' => ['sourceField' => 'MA_09', 'type' => ParameterType::STRING],
            'ma10' => ['sourceField' => 'MA_10', 'type' => ParameterType::STRING],
            'ma11' => ['sourceField' => 'MA_11', 'type' => ParameterType::STRING],
            'ma12' => ['sourceField' => 'MA_12', 'type' => ParameterType::STRING],
            'waluta' => ['sourceField' => 'WALUTA', 'type' => ParameterType::STRING],
            'lp' => ['sourceField' => 'LP', 'type' => ParameterType::INTEGER],
            'alert' => ['sourceField' => 'ALERT', 'type' => ParameterType::INTEGER],
            'opis' => ['sourceField' => 'OPIS', 'type' => ParameterType::INTEGER],
            'kod_anal' => ['sourceField' => 'KOD_ANAL', 'type' => ParameterType::STRING],
            'kod_zkonta' => ['sourceField' => 'KOD_ZKONTA', 'type' => ParameterType::STRING],
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
