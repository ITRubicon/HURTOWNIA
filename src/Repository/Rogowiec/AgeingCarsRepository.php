<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class AgeingCarsRepository extends IApiRepository
{
    private string $endpoint = '/wdf/Reports/vehicleStock?id_org={orgId}';
    protected $table = 'rogowiec_ageing_cars';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $branches = $this->getBranch();
        $branchesCount = count($branches);
        $resCount = 0;

        if ($branchesCount) {
            $i = 1;

            foreach ($branches as $b) {
                echo "\nId jednostki $b ----> $i/$branchesCount";

                $url = str_replace('{orgId}', $b, $this->endpoint);
                $this->fetchResult = $this->fetchApiResult($url);

                $this->fetchResult = $this->fetchApiResult($url);
                $resCount += count($this->fetchResult);
                $this->save();
                $this->clearDataArrays();
                $i++;
            }
        } else 
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą jednostki organizacyjne [rogowiec:org-unit]", -1);

        return ['fetched' => $resCount];
    }

    private function getBranch(): array
    {
        $q = "SELECT dms_id FROM rogowiec_org_unit WHERE rodzaj LIKE 'Magazyn pojazd%' AND source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'oddzial' => ['sourceField' => 'NazwaOddzialu', 'type' => ParameterType::STRING],
            'jednostka_org_kod' => ['sourceField' => 'KodJo', 'type' => ParameterType::STRING],
            'jednostka_org_nazwa' => ['sourceField' => 'SkrotLabel', 'type' => ParameterType::STRING],
            'dokument_nr' => ['sourceField' => 'NumerTxt', 'type' => ParameterType::STRING],
            'dokument_symbol' => ['sourceField' => 'SymbolDokumentu', 'type' => ParameterType::STRING],
            'dokument_data' => ['sourceField' => 'DataDok', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'auto_wartosc' => ['sourceField' => 'WartSamNetto', 'type' => ParameterType::STRING],
            'wskaznik_stan' => ['sourceField' => 'WskStanu', 'type' => ParameterType::STRING],
            'auto_rodzaj' => ['sourceField' => 'RodzSam', 'type' => ParameterType::STRING],
            'wydanie_data' => ['sourceField' => 'DtWydania', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'vin' => ['sourceField' => 'Vin', 'type' => ParameterType::STRING],
            'rejestracja_nr' => ['sourceField' => 'NrRej', 'type' => ParameterType::STRING],
            'rejestracja_data' => ['sourceField' => 'DataRej', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'opis' => ['sourceField' => 'Opis', 'type' => ParameterType::STRING],
            'dokument_rozchod' => ['sourceField' => 'HdlDokRozch', 'type' => ParameterType::STRING],
            'producent' => ['sourceField' => 'Producent', 'type' => ParameterType::STRING],
            'marka' => ['sourceField' => 'Marka', 'type' => ParameterType::STRING],
            'przeglad_zero' => ['sourceField' => 'PrzegladZero', 'type' => ParameterType::STRING],
            'wsk_dok_akc' => ['sourceField' => 'WskDokAkc', 'type' => ParameterType::STRING],
            'wskaznik_najmu' => ['sourceField' => 'WskNajmu', 'type' => ParameterType::STRING],
            'ostatnia_rezerwacja_data' => ['sourceField' => 'DtEndLastRez', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'rezerwujacy' => ['sourceField' => 'Rezerwujacy', 'type' => ParameterType::STRING],
            'klient' => ['sourceField' => 'Klient', 'type' => ParameterType::STRING],
            'sprzedawca' => ['sourceField' => 'Sprzedawca', 'type' => ParameterType::STRING],
            'zamowienie_nr' => ['sourceField' => 'NumerZam', 'type' => ParameterType::STRING],
            'zamowienie_data' => ['sourceField' => 'DataZam', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'plan_odbior_data' => ['sourceField' => 'DataOdbPlan', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'zamowienie_opis' => ['sourceField' => 'OpisZam', 'type' => ParameterType::STRING],
            'wskaznik_przekazania' => ['sourceField' => 'WskPrzekazSam', 'type' => ParameterType::STRING],
            'odkup_rodzaj' => ['sourceField' => 'RodzajOdkupu', 'type' => ParameterType::STRING],
            'odkupujacy' => ['sourceField' => 'Odkupujacy', 'type' => ParameterType::STRING],
            'fv_zakup' => ['sourceField' => 'NumerDokZrodl', 'type' => ParameterType::STRING],
            'fv_zakup_data' => ['sourceField' => 'DataDokZrodl', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'platnosci' => ['sourceField' => 'Platnosci', 'type' => ParameterType::STRING],
            'liczba_dni_stan' => ['sourceField' => 'LiczbaDniStan', 'type' => ParameterType::INTEGER],
            'wskaznik_1' => ['sourceField' => 'Wskaznik1', 'type' => ParameterType::STRING],
            'wskaznik_2' => ['sourceField' => 'Wskaznik2', 'type' => ParameterType::STRING],
            'wskaznik_3' => ['sourceField' => 'Wskaznik3', 'type' => ParameterType::STRING],
            'wskaznik_4' => ['sourceField' => 'Wskaznik4', 'type' => ParameterType::STRING],
            'wskaznik_5' => ['sourceField' => 'Wskaznik5', 'type' => ParameterType::STRING],
            'wskaznik_6' => ['sourceField' => 'Wskaznik6', 'type' => ParameterType::STRING],
            'wskaznik_7' => ['sourceField' => 'Wskaznik7', 'type' => ParameterType::STRING],
            'uwaga_1' => ['sourceField' => 'TbUwagaB1', 'type' => ParameterType::STRING],
            'uwaga_2' => ['sourceField' => 'TbUwagaB2', 'type' => ParameterType::STRING],
            'uwaga_3' => ['sourceField' => 'TbUwagaB3', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}