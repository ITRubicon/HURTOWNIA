<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class AgeingProductionRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/ous051?id_org={id_org}&data={date_to}';
    protected $table = 'rogowiec_ageing_production';

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
                $this->save();
                $this->clearDataArrays();
                $i++;
            }
        } else 
        throw new \Exception("Nie żadnych jednostek organizacyjnych dla " . $this->source->getName() . ". Najpierw uruchom komendę pobierającą jednostki organizacyjne [rogowiec:orgunit]", -1);

        return ['fetched' => $resCount];
    }

    private function fetchBranchId()
    {
        
        $q = "SELECT dms_id FROM rogowiec_org_unit rou WHERE source = :source AND rodzaj LIKE 'Serwis%'";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'zlecenie_nr' => ['sourceField' => 'NrZlecenia', 'type' => ParameterType::STRING],
            'zlecenie_data' => ['sourceField' => 'DataZlecenia', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'indeks' => ['sourceField' => 'Indeks', 'type' => ParameterType::STRING],
            'ilosc' => ['sourceField' => 'Ilosc', 'type' => ParameterType::STRING],
            'cena_zakup' => ['sourceField' => 'CenaZakupu', 'type' => ParameterType::STRING],
            'wartosc_zakup' => ['sourceField' => 'WartoscZakupu', 'type' => ParameterType::STRING],
            'czesci_nazwa' => ['sourceField' => 'CzesciNazwa', 'type' => ParameterType::STRING],
            'czesci_nazwa_2' => ['sourceField' => 'CzesciNazwa2', 'type' => ParameterType::STRING],
            'nr_magazynowy' => ['sourceField' => 'NrMagazynowy', 'type' => ParameterType::STRING],
            'dokument_magazynowy_data' => ['sourceField' => 'DataDok', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'kod_interwencja' => ['sourceField' => 'TypInterwencji', 'type' => ParameterType::STRING],
            'platnosc_kanal' => ['sourceField' => 'KanalPlatnosci', 'type' => ParameterType::STRING],
            'rodzaj' => ['sourceField' => 'Rodzaj', 'type' => ParameterType::STRING],
            'jednostka' => ['sourceField' => 'JM', 'type' => ParameterType::STRING],
            'dokument_sumbol' => ['sourceField' => 'SymbolDokumentu', 'type' => ParameterType::STRING],
            'kod_klienta' => ['sourceField' => 'KodKlienta', 'type' => ParameterType::STRING],
            'klient_nazwa' => ['sourceField' => 'Klient', 'type' => ParameterType::STRING],
            'odbierajacy' => ['sourceField' => 'Odbierajacy', 'type' => ParameterType::STRING],
            'prowadzacy' => ['sourceField' => 'Prowadzacy', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'VIN', 'type' => ParameterType::STRING],
            'rejestracja_nr' => ['sourceField' => 'NrRejestracyjny', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
