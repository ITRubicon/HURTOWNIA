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
            throw new \Exception("Nie żadnych oddziałów dla " . $this->source->getName() . ". Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:branch]", 99);

        return ['fetched' => $resCount];
    }

    public function archiveInvoices()
    {
        $q = "INSERT INTO rogowiec_invoice_archive (source, `number`, doc_date, sale_date, net_value, gross_value, worker, customer_code, platnosci, metoda_platnosci, status_platnosci)
            SELECT * FROM (
            SELECT source, fv_numer, fv_data, fv_data AS sale_date, sprzedaz_netto, CAST(sprzedaz_netto * 1.23 AS decimal(12,2)), pracownik, kod_odbiorca, platnosci,
                TRIM(REGEXP_REPLACE(REGEXP_REPLACE(platnosci, '.+Forma:', ''), ';.+', '')) AS metoda_platnosci,
                CASE TRIM(REGEXP_REPLACE(REGEXP_REPLACE(platnosci, '.+Rozliczenie:', ''), '[^KCB]', ''))
                    WHEN 'C' THEN 'opłacone częściowo'
                    WHEN 'B' THEN 'nieopłacone'
                    WHEN 'K' THEN 'rozliczone'
                    ELSE 'nieznany'
                END AS status_platnosci
            FROM rogowiec_cars_sold
            WHERE source = :source
            ) r
            ON DUPLICATE KEY UPDATE
            platnosci = r.platnosci,
            worker = r.pracownik,
            customer_code = r.kod_odbiorca,
            metoda_platnosci = r.metoda_platnosci,
            status_platnosci = r.status_platnosci
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    private function fetchBranchId()
    {
        $q = "SELECT dms_id FROM rogowiec_branch WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'pracownik' => ['sourceField' => 'Pracownik', 'type' => ParameterType::STRING],
            'zamawiajacy' => ['sourceField' => 'Zamawiajacy', 'type' => ParameterType::STRING],
            'fv_numer' => ['sourceField' => 'NumerFakturySprzedazy', 'type' => ParameterType::STRING],
            'fv_data' => ['sourceField' => 'DataSprzedazy', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'vin' => ['sourceField' => 'Vin', 'type' => ParameterType::STRING],
            'marka' => ['sourceField' => 'Marka', 'type' => ParameterType::STRING],
            'model' => ['sourceField' => 'Model', 'type' => ParameterType::STRING],
            'wersja' => ['sourceField' => 'Wersja', 'type' => ParameterType::STRING],
            'nr_wydanie' => ['sourceField' => 'NumerWydania', 'type' => ParameterType::STRING],
            'data_wydanie' => ['sourceField' => 'DataWydania', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'data_wydanie_klient' => ['sourceField' => 'DataWydaniaKlient', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'sprzedaz_netto' => ['sourceField' => 'SprzedazNetto', 'type' => ParameterType::STRING],
            'korekty_wartosc' => ['sourceField' => 'KorektyWartosc', 'type' => ParameterType::STRING],
            'rezerwy_wartosc' => ['sourceField' => 'RezerwyWartosc', 'type' => ParameterType::STRING],
            'zcs_wartosc' => ['sourceField' => 'ZcsWartosc', 'type' => ParameterType::STRING],
            'kks_wartosc' => ['sourceField' => 'KksWartosc', 'type' => ParameterType::STRING],
            'zakup_wartosc' => ['sourceField' => 'ZakupWartosc', 'type' => ParameterType::STRING],
            'usterki_koszty_wartosc' => ['sourceField' => 'DoposazenieDealerWartosc', 'type' => ParameterType::STRING],
            'usterki_platne_wartosc' => ['sourceField' => 'DoposazenieKlientWartosc', 'type' => ParameterType::STRING],
            'prowizja_kredyt' => ['sourceField' => 'ProwizjaKredytyWartosc', 'type' => ParameterType::STRING],
            'zaliczki' => ['sourceField' => 'Zaliczki', 'type' => ParameterType::STRING],
            'kod_nabywca' => ['sourceField' => 'KodNabywca', 'type' => ParameterType::STRING],
            'kod_odbiorca' => ['sourceField' => 'KodOdbiorca', 'type' => ParameterType::STRING],
            'klasyfikacja_sprzedaz' => ['sourceField' => 'KlasyfikacjaSprzed', 'type' => ParameterType::STRING],
            'id_zamowienie' => ['sourceField' => 'IdZamowienia', 'type' => ParameterType::STRING],
            'platnosci' => ['sourceField' => 'Platnosci', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
