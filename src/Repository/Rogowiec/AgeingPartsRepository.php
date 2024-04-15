<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class AgeingPartsRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/omc031?id_org={id_org}&data_do={date_to}';
    protected $table = 'rogowiec_ageing_parts';

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
                    ['{id_org}', '{date_to}'],
                    [$id, $this->dateTo],
                    $this->endpoint
                );

                $this->fetchResult = $this->fetchApiResult($url);
                $resCount += count($this->fetchResult);
                $this->addIdOrg($id);
                $this->save();
                $this->clearDataArrays();
                $i++;
            }
        } else 
            throw new \Exception("Nie żadnych jednostek organizacyjnych dla " . $this->source->getName() . ". Najpierw uruchom komendę pobierającą jednostki organizacyjne [rogowiec:orgunit]", -1);

        return ['fetched' => $resCount];
    }

    private function addIdOrg(int $id)
    {
        foreach ($this->fetchResult as &$r) {
            $r['id_org'] = $id;
        }
        unset($tablica);
    }

    private function fetchBranchId()
    {
        
        $q = "SELECT dms_id FROM rogowiec_org_unit rou WHERE source = :source AND rodzaj LIKE 'Magazyn cz%'";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'producent' => ['sourceField' => 'Producent', 'type' => ParameterType::STRING],
            'indeks' => ['sourceField' => 'Indeks', 'type' => ParameterType::STRING],
            'dostawa_data' => ['sourceField' => 'DataDostawy', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'dokument_nr' => ['sourceField' => 'NrDokumentu', 'type' => ParameterType::STRING],
            'dokument_symbol' => ['sourceField' => 'SymbDok', 'type' => ParameterType::STRING],
            'jednostka' => ['sourceField' => 'JM', 'type' => ParameterType::STRING],
            'nazwa' => ['sourceField' => 'Nazwa', 'type' => ParameterType::STRING],
            'zastosowanie' => ['sourceField' => 'Zastosowanie', 'type' => ParameterType::STRING],
            'lokalizacja_magazyn' => ['sourceField' => 'Lokalizacja', 'type' => ParameterType::STRING],
            'dostawca_kod' => ['sourceField' => 'KodDostawcy', 'type' => ParameterType::STRING],
            'dostawca_nazwa' => ['sourceField' => 'Dostawca', 'type' => ParameterType::STRING],
            'segment' => ['sourceField' => 'Segment', 'type' => ParameterType::STRING],
            'grupa' => ['sourceField' => 'Grupa', 'type' => ParameterType::STRING],
            'rodzina' => ['sourceField' => 'Rodzina', 'type' => ParameterType::STRING],
            'rodzaj_kod' => ['sourceField' => 'KodRodzaj', 'type' => ParameterType::STRING],
            'stan_ilosc' => ['sourceField' => 'Stan', 'type' => ParameterType::STRING],
            'wartosc_netto' => ['sourceField' => 'WartNetto', 'type' => ParameterType::STRING],
            'ilosc_rezerwacja' => ['sourceField' => 'IloscRez', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
            'id_org' => ['sourceField' => 'id_org', 'type' => ParameterType::INTEGER],
        ];
    }
}
