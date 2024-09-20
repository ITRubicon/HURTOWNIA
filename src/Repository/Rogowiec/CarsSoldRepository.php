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
        $q = "INSERT INTO rogowiec_invoice_archive (source, `number`, doc_date, sale_date, net_value, gross_value, worker, customer_code, platnosci)
            SELECT source, fv_numer, fv_data, fv_data, sprzedaz_netto, CAST(sprzedaz_netto * 1.23 AS decimal(12,2)), pracownik, kod_odbiorca, platnosci FROM rogowiec_cars_sold r
            WHERE source = :source
            ON DUPLICATE KEY UPDATE
            platnosci = r.platnosci
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
        // tymczasowe obejście - Rogowiec zmienił kontrakt w API
        $changedApis = ['jbr_smora', 'jbr_jaremko', 'jbr_bmw', 'jbr', 'jbr_jlr'];
        if (in_array($this->source->getName(), $changedApis)) {
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
