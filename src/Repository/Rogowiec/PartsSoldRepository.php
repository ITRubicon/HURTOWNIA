<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class PartsSoldRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/partsDataRaport?id_oddzial={id_oddzial}&data_od={date_from}&data_do={date_to}';
    protected $table = 'rogowiec_parts_sold';

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
            SELECT source, dokument_numer, `data`, `data` AS sale_date, SUM(sprzedaz_wartosc_netto), CAST(SUM(sprzedaz_wartosc_netto * 1.23) AS decimal(12,2)), pracownik, kod_klienta, platnosci,
            TRIM(REGEXP_REPLACE(REGEXP_REPLACE(platnosci, '.+Forma:', ''), ';.+', '')) AS metoda_platnosci,
            CASE TRIM(REGEXP_REPLACE(REGEXP_REPLACE(platnosci, '.+Rozliczenie:', ''), '[^KCB]', ''))
                WHEN 'C' THEN 'opłacone częściowo'
                WHEN 'B' THEN 'nieopłacone'
                WHEN 'K' THEN 'rozliczone'
                ELSE 'nieznany'
            END AS status_platnosci
            FROM rogowiec_parts_sold
            WHERE (rodzaj = 'Sklep / Klient' OR klient_rodzaj IN ('Pozostali', 'Indywidualny'))
                AND source = :source
            GROUP BY source, dokument_numer, `data`, pracownik, kod_klienta, platnosci
            ) r
            ON DUPLICATE KEY UPDATE
            worker = r.pracownik,
            customer_code = r.kod_klienta,
            platnosci = r.platnosci,
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
            'rodzaj' => ['sourceField' => 'Rodzaj', 'type' => ParameterType::STRING],
            'dokument_numer' => ['sourceField' => 'NumerDokSprzed', 'type' => ParameterType::STRING],
            'data' => ['sourceField' => 'DataDokSprzed', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'klient_rodzaj' => ['sourceField' => 'RodzajKlienta', 'type' => ParameterType::STRING],
            'sprzedaz_wartosc_netto' => ['sourceField' => 'WartoscNettoSprzedazy', 'type' => ParameterType::STRING],
            'zakup_wartosc_netto' => ['sourceField' => 'WartoscNettoZakupu', 'type' => ParameterType::STRING],
            'indeks' => ['sourceField' => 'Indeks', 'type' => ParameterType::STRING],
            'nazwa' => ['sourceField' => 'Nazwa', 'type' => ParameterType::STRING],
            'producent' => ['sourceField' => 'Producent', 'type' => ParameterType::STRING],
            'segment' => ['sourceField' => 'Segment', 'type' => ParameterType::STRING],
            'ilosc' => ['sourceField' => 'Ilosc', 'type' => ParameterType::STRING],
            'kod_klienta' => ['sourceField' => 'KodKlienta', 'type' => ParameterType::STRING],
            'klasyfikacja_sprzedaz' => ['sourceField' => 'KlasyfikacjaSprzed', 'type' => ParameterType::STRING],
            'pracownik' => ['sourceField' => 'Pracownik', 'type' => ParameterType::STRING],
            'platnosci' => ['sourceField' => 'Platnosci', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
