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
            'nazwa' => ['sourceField' => 'Producent', 'type' => ParameterType::STRING],
            'producent' => ['sourceField' => 'Segment', 'type' => ParameterType::STRING],
            'segment' => ['sourceField' => 'StatusZam', 'type' => ParameterType::STRING],
            'ilosc' => ['sourceField' => 'Ilosc', 'type' => ParameterType::STRING],
            'kod_klienta' => ['sourceField' => 'KodKlienta', 'type' => ParameterType::STRING],
            'klasyfikacja_sprzedaz' => ['sourceField' => 'KlasyfikacjaSprzed', 'type' => ParameterType::STRING],
            'pracownik' => ['sourceField' => 'Pracownik', 'type' => ParameterType::STRING],
            'platnosci' => ['sourceField' => 'Platnosci', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
