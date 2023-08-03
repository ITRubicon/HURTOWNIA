<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarsSoldRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/vehicleSaleReport?id_oddzial={id_oddzial}&data_od={date_from}&data_do={date_to}';
    protected $table = 'rogowiec_cars_sold';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $branchesIds = $this->fetchBranchId();
        $branchesCount = count($branchesIds);
        $resCount = 0;

        if ($branchesCount) {
            $i = 1;

            foreach ($branchesIds as $id) {
                echo "\nId jednostki $id ----> $i/$branchesCount";
                $url = str_replace(
                    ['{id_oddzial}', '{date_from}', '{date_to}'],
                    [$id, $this->dateFrom, $this->dateTo],
                    $this->endpoint
                );

                $this->fetchResult = $this->fetchApiResult($url);
                $resCount += count($this->fetchResult);
                $this->save();
                $this->clearDataArrays();
                $i++;
            }
        } else 
            throw new \Exception("Nie żadnych . Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:branch]", 99);

        return ['fetched' => $resCount];
    }

    private function fetchBranchId()
    {
        $q = "SELECT dms_id FROM rogowiec_branch WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'pracownik' => ['sourceField' => 'PRACOWNIK', 'type' => ParameterType::STRING],
            'zamawiajacy' => ['sourceField' => 'ZAMAWIAJACY', 'type' => ParameterType::STRING],
            'fv_numer' => ['sourceField' => 'NUMER_FAKTURY_SPRZEDAZY', 'type' => ParameterType::STRING],
            'fv_data' => ['sourceField' => 'DATA_SPRZEDAZY', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'vin' => ['sourceField' => 'VIN', 'type' => ParameterType::STRING],
            'marka' => ['sourceField' => 'MARKA', 'type' => ParameterType::STRING],
            'model' => ['sourceField' => 'MODEL', 'type' => ParameterType::STRING],
            'wersja' => ['sourceField' => 'WERSJA', 'type' => ParameterType::STRING],
            'nr_wydanie' => ['sourceField' => 'NUMER_TXT_512', 'type' => ParameterType::STRING],
            'data_wydanie' => ['sourceField' => 'DATA_512', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'data_wydanie_klient' => ['sourceField' => 'DATA_PRZEKAZANIA_511', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'sprzedaz_netto' => ['sourceField' => 'SPRZEDAZ_NETTO', 'type' => ParameterType::STRING],
            'korekty_wartosc' => ['sourceField' => 'KOREKTY_WARTOSC', 'type' => ParameterType::STRING],
            'rezerwy_wartosc' => ['sourceField' => 'REZERWY_WARTOSC', 'type' => ParameterType::STRING],
            'zcs_wartosc' => ['sourceField' => 'ZCS_WARTOSC', 'type' => ParameterType::STRING],
            'kks_wartosc' => ['sourceField' => 'KKS_WARTOSC', 'type' => ParameterType::STRING],
            'zakup_wartosc' => ['sourceField' => 'ZAKUP_WARTOSC', 'type' => ParameterType::STRING],
            'usterki_koszty_wartosc' => ['sourceField' => 'USTERKI_KOSZTY_WARTOSC', 'type' => ParameterType::STRING],
            'usterki_platne_wartosc' => ['sourceField' => 'USTERKI_PLATNE_WARTOSC', 'type' => ParameterType::STRING],
            'prowizja_kredyt' => ['sourceField' => 'PROWIZJA_KREDYTY_WARTOSC', 'type' => ParameterType::STRING],
            'zaliczki' => ['sourceField' => 'ZALICZKI', 'type' => ParameterType::STRING],
            'kod_nabywca' => ['sourceField' => 'KOD_200_NABYWCA', 'type' => ParameterType::STRING],
            'kod_odbiorca' => ['sourceField' => 'KOD_200_ODBIORCA', 'type' => ParameterType::STRING],
            'klasyfikacja_sprzedaz' => ['sourceField' => 'ID_149_02_KLASYFIK_215', 'type' => ParameterType::STRING],
            'id_zamowienie' => ['sourceField' => 'ID_ZAM', 'type' => ParameterType::STRING],
            'platnosci' => ['sourceField' => 'PLATNOSCI', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
