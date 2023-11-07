<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceSoldRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/svcDataReport?id_org={id_org}&data_od={date_from}&data_do={date_to}';
    protected $table = 'rogowiec_service_sold';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $orgUnitsIds = $this->fetchBranchId();
        $branchesCount = count($orgUnitsIds);
        $resCount = 0;

        if ($branchesCount) {
            $i = 1;

            foreach ($orgUnitsIds as $id) {
                echo "\nId jednostki $id ----> $i/$branchesCount";
                $url = str_replace(
                    ['{id_org}', '{date_from}', '{date_to}'],
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
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą jednostki organizacyjne [rogowiec:orgunit]", -1);

        return ['fetched' => $resCount];
    }

    private function fetchBranchId()
    {
        $q = "SELECT dms_id FROM rogowiec_org_unit rou WHERE source = :source AND rodzaj LIKE 'Serwis%' OR nazwa LIKE '%skp%";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'serwis_nr' => ['sourceField' => 'NrSerwisu', 'type' => ParameterType::STRING],
            'zlecenie_data' => ['sourceField' => 'DataZlecenia', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'marka' => ['sourceField' => 'Marka', 'type' => ParameterType::STRING],
            'model' => ['sourceField' => 'NazwaModelu', 'type' => ParameterType::STRING],
            'zlecenie_nr' => ['sourceField' => 'NumerZlecenia', 'type' => ParameterType::STRING],
            'kod_interwencja' => ['sourceField' => 'KodInterwencji', 'type' => ParameterType::STRING],
            'silnik_typ' => ['sourceField' => 'TypSilnika', 'type' => ParameterType::STRING],
            'rejestracja_nr' => ['sourceField' => 'NumerRejestracyjny', 'type' => ParameterType::STRING],
            'rok_produkcja' => ['sourceField' => 'NumerRejestracyjny', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'VIN', 'type' => ParameterType::STRING],
            'rok_produkcja' => ['sourceField' => 'RokProdukcji', 'type' => ParameterType::INTEGER],
            'kod_klienta' => ['sourceField' => 'KodKlienta', 'type' => ParameterType::STRING],
            'klient_nazwa' => ['sourceField' => 'NazwaKlienta', 'type' => ParameterType::STRING],
            'usluga_punkt_sprzedazy' => ['sourceField' => 'UsługaPunktSprzedazy', 'type' => ParameterType::STRING],
            'platnik' => ['sourceField' => 'RodzajPlatnika', 'type' => ParameterType::STRING],
            'dokument_status' => ['sourceField' => 'StatusDokumentu', 'type' => ParameterType::STRING],
            'fv_numer' => ['sourceField' => 'NumerDokumentu', 'type' => ParameterType::STRING],
            'dokument_typ' => ['sourceField' => 'TypDokumentu', 'type' => ParameterType::STRING],
            'fv_data' => ['sourceField' => 'DataWystawienia', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'stawka' => ['sourceField' => 'Stawka', 'type' => ParameterType::STRING],
            'pozycja_typ' => ['sourceField' => 'TypPozycji', 'type' => ParameterType::STRING],
            'pozycja_symbol' => ['sourceField' => 'SymbolPozycji', 'type' => ParameterType::STRING],
            'segment' => ['sourceField' => 'Segment', 'type' => ParameterType::STRING],
            'pozycja_nazwa' => ['sourceField' => 'NazwaPozycji', 'type' => ParameterType::STRING],
            'koszt' => ['sourceField' => 'WartoscEwidencyjna', 'type' => ParameterType::STRING],
            'wartosc' => ['sourceField' => 'WartoscNetto', 'type' => ParameterType::STRING],
            'rabat_procent' => ['sourceField' => 'RabatProcent', 'type' => ParameterType::STRING],
            'rabat_wartosc' => ['sourceField' => 'RabatWartosc', 'type' => ParameterType::STRING],
            'marza' => ['sourceField' => 'Marza', 'type' => ParameterType::STRING],
            'jednostka' => ['sourceField' => 'Jednostka', 'type' => ParameterType::STRING],
            'ilosc' => ['sourceField' => 'Ilosc', 'type' => ParameterType::STRING],
            'jednostka_czas' => ['sourceField' => 'JC', 'type' => ParameterType::STRING, 'format' => ['dec' => true]],
            'doradca' => ['sourceField' => 'Doradca', 'type' => ParameterType::STRING],
            'mechanik' => ['sourceField' => 'Mechanik', 'type' => ParameterType::STRING],
            'firma_ubezpieczeniowa' => ['sourceField' => 'FirmaUbezpieczeniowa', 'type' => ParameterType::STRING],
            'szkoda_nr' => ['sourceField' => 'NumerSzkody', 'type' => ParameterType::STRING],
            'fv_wystawiajacy' => ['sourceField' => 'Wystawiajacy', 'type' => ParameterType::STRING],
            'zlecenie_czas_zamkniecia' => ['sourceField' => 'CzasZamknieciaZlecenia', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'przekazal_salon' => ['sourceField' => 'PrzekazanoZSalonu', 'type' => ParameterType::STRING],
            'klasyfikacja_sprzedaz' => ['sourceField' => 'KlasyfikacjaSprzed', 'type' => ParameterType::STRING],
            'platnosci' => ['sourceField' => 'Platnosci', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
