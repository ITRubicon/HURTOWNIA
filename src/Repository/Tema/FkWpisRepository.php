<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FkWpisRepository extends IApiRepository
{
    private string $endpoint = '/api/accounting/v3/01/dta/row-data?tid=FK.WPIS';
    protected $table = 'tema_fk_zapisy';

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
            'lp' => ['sourceField' => 'LP', 'type' => ParameterType::INTEGER],
            'z_dnia' => ['sourceField' => 'Z_DNIA', 'type' => ParameterType::STRING],
            'data_dok' => ['sourceField' => 'DATA_DOK', 'type' => ParameterType::STRING],
            'tresc' => ['sourceField' => 'TRESC', 'type' => ParameterType::STRING],
            'kwota' => ['sourceField' => 'KWOTA', 'type' => ParameterType::STRING],
            'winien' => ['sourceField' => 'WINIEN', 'type' => ParameterType::STRING],
            'ma' => ['sourceField' => 'MA', 'type' => ParameterType::STRING],
            'numer_dow' => ['sourceField' => 'NUMER_DOW', 'type' => ParameterType::STRING],
            'pozyc_dow' => ['sourceField' => 'POZYC_DOW', 'type' => ParameterType::STRING],
            'faktura' => ['sourceField' => 'FAKTURA', 'type' => ParameterType::STRING],
            'konto_p' => ['sourceField' => 'KONTO_P', 'type' => ParameterType::STRING],
            'termin' => ['sourceField' => 'TERMIN', 'type' => ParameterType::STRING],
            'nr_pozycji' => ['sourceField' => 'NR_POZYCJI', 'type' => ParameterType::INTEGER],
            'id_progr' => ['sourceField' => 'ID_PROGR', 'type' => ParameterType::INTEGER],
            'id_uz' => ['sourceField' => 'ID_UZ', 'type' => ParameterType::INTEGER],
            'kod_oper' => ['sourceField' => 'KOD_OPER', 'type' => ParameterType::INTEGER],
            'data_zapis' => ['sourceField' => 'DATA_ZAPIS', 'type' => ParameterType::STRING],
            'dziennik' => ['sourceField' => 'DZIENNIK', 'type' => ParameterType::INTEGER],
            'waluta_wn' => ['sourceField' => 'WALUTA_WN', 'type' => ParameterType::STRING],
            'waluta_ma' => ['sourceField' => 'WALUTA_MA', 'type' => ParameterType::STRING],
            'waluta_ku' => ['sourceField' => 'WALUTA_KU', 'type' => ParameterType::STRING],
            'id_wpisu' => ['sourceField' => 'ID_WPISU', 'type' => ParameterType::STRING],
            'dow_podst' => ['sourceField' => 'DOW_PODST', 'type' => ParameterType::STRING],
            'fk' => ['sourceField' => 'FK', 'type' => ParameterType::STRING],
            'kod_ak' => ['sourceField' => 'KOD_AK', 'type' => ParameterType::INTEGER],
            'kod_ak2' => ['sourceField' => 'KOD_AK2', 'type' => ParameterType::INTEGER],
            'region' => ['sourceField' => 'REGION', 'type' => ParameterType::STRING],
            'kod_kontr' => ['sourceField' => 'KOD_KONTR', 'type' => ParameterType::STRING],
            'id_dokum' => ['sourceField' => 'ID_DOKUM', 'type' => ParameterType::STRING],
            'char_plat' => ['sourceField' => 'CHAR_PLAT', 'type' => ParameterType::STRING],
            'uwagi' => ['sourceField' => 'UWAGI', 'type' => ParameterType::STRING],
            'data_wplyw' => ['sourceField' => 'DATA_WPLYW', 'type' => ParameterType::STRING],
            'wart_dok' => ['sourceField' => 'WART_DOK', 'type' => ParameterType::STRING],
            'wart_vat' => ['sourceField' => 'WART_VAT', 'type' => ParameterType::STRING],
            'wplata' => ['sourceField' => 'WPLATA', 'type' => ParameterType::STRING],
            'wart_wal_d' => ['sourceField' => 'WART_WAL_D', 'type' => ParameterType::STRING],
            'kod_rej' => ['sourceField' => 'KOD_REJ', 'type' => ParameterType::STRING],
            'fid_dokum' => ['sourceField' => 'FID_DOKUM', 'type' => ParameterType::STRING],
            'cargo' => ['sourceField' => 'CARGO', 'type' => ParameterType::STRING],
            'oddzial' => ['sourceField' => 'ODDZIAL', 'type' => ParameterType::STRING],
            'typrozr' => ['sourceField' => 'TYPROZR', 'type' => ParameterType::STRING],
            'status' => ['sourceField' => 'STATUS', 'type' => ParameterType::STRING],
            'id_platn' => ['sourceField' => 'ID_PLATN', 'type' => ParameterType::STRING],
            'id_zlec' => ['sourceField' => 'ID_ZLEC', 'type' => ParameterType::STRING],
            'nr_zlecenia' => ['sourceField' => 'NRZLECENIA', 'type' => ParameterType::STRING],
            'id_zrodla' => ['sourceField' => 'ID_ZRODLA', 'type' => ParameterType::STRING],
            'id_rozbic' => ['sourceField' => 'ID_ROZBIC', 'type' => ParameterType::STRING],
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
